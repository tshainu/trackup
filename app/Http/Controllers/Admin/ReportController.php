<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\Employee;
use App\Models\PaymentLog;
use App\Models\DeliveredOrder;
use App\Models\StoreInfo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // Shared: resolve period + build all report data
    // ─────────────────────────────────────────────────────────────
    private function resolveData(Request $request): array
    {
        $period = $request->input('period', 'month');
        $today  = Carbon::today();

        switch ($period) {
            case 'today':
                $from = $today->toDateString(); $to = $today->toDateString(); break;
            case 'week':
                $from = $today->copy()->startOfWeek()->toDateString(); $to = $today->toDateString(); break;
            case 'year':
                $from = $today->copy()->startOfYear()->toDateString(); $to = $today->toDateString(); break;
            case 'custom':
                $from = $request->input('from', $today->copy()->startOfMonth()->toDateString());
                $to   = $request->input('to',   $today->toDateString()); break;
            default:
                $period = 'month';
                $from = $today->copy()->startOfMonth()->toDateString(); $to = $today->toDateString();
        }

        $report = $request->input('report', 'jobs');
        $base   = JobCard::with(['employee', 'invoiceItems', 'paymentLogs'])->whereBetween('date', [$from.' 00:00:00', $to.' 23:59:59']);

        // 1. Jobs
        $jobs = (clone $base)->orderByDesc('date')->get();
        $jobSummary = [
            'total'         => $jobs->count(),
            'completed'     => $jobs->where('status', 'Completed')->count(),
            'in_progress'   => $jobs->where('status', 'In Progress')->count(),
            'pending'       => $jobs->where('status', 'Pending')->count(),
            'not_completed' => $jobs->where('status', 'Not Completed')->count(),
            'broken'        => $jobs->where('status', 'Broken')->count(),
            'cancelled'     => $jobs->where('status', 'Cancelled')->count(),
            'by_device'     => $jobs->groupBy('device_name')->map->count()->sortDesc()->take(8),
            'by_brand'      => $jobs->whereNotNull('device_brand')->groupBy('device_brand')->map->count()->sortDesc()->take(8),
        ];

        // 2. Payment
        $paymentJobs = (clone $base)->where(function($q){
            $q->where('paid_amount', '>', 0)->orWhere('payment_received', true);
        })->orderByDesc('date')->get();
        $paymentSummary = [
            'total_collected'  => $paymentJobs->sum('paid_amount'),
            'fully_paid_count' => $paymentJobs->where('payment_received', true)->count(),
            'partial_count'    => $paymentJobs->where('payment_received', false)->where('paid_amount', '>', 0)->count(),
            'transactions'     => $paymentJobs->count(),
        ];
        $paymentLogs = PaymentLog::with('jobCard')
            ->whereBetween('paid_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->orderByDesc('paid_at')->get();

        // 3. Revenue
        $revenueJobs = (clone $base)->orderByDesc('date')->get();
        $revenueSummary = [
            'grand_total_billed' => $revenueJobs->sum(fn($j) => $j->grand_total),
            'total_collected'    => $revenueJobs->sum('paid_amount'),
            'outstanding'        => $revenueJobs->sum(fn($j) => $j->balance),
            'fully_paid'         => $revenueJobs->where('payment_received', true)->sum('paid_amount'),
            'partial_paid'       => $revenueJobs->where('payment_received', false)->where('paid_amount', '>', 0)->sum('paid_amount'),
            'unpaid_count'       => $revenueJobs->where('payment_received', false)->where('paid_amount', 0)->count(),
        ];

        // 4. Status
        $statusJobs   = (clone $base)->orderBy('status')->orderByDesc('date')->get();
        $statusGroups = $statusJobs->groupBy('status');

        // 5. Overdue
        $overdueJobs = JobCard::with('employee')
            ->whereNotNull('estimated_delivery')
            ->where('estimated_delivery', '<', $today)
            ->whereNotIn('status', ['Completed', 'Broken'])
            ->orderBy('estimated_delivery')->get();

        // 6. Undelivered
        $undeliveredJobs = JobCard::with(['employee', 'invoiceItems'])
            ->where('status', 'Completed')->orderByDesc('date')->get();
        $undeliveredSummary = [
            'total'       => $undeliveredJobs->count(),
            'paid'        => $undeliveredJobs->where('payment_received', true)->count(),
            'outstanding' => $undeliveredJobs->where('payment_received', false)->count(),
            'amount_due'  => $undeliveredJobs->sum(fn($j) => $j->balance),
        ];

        // 7. Staff
        $employees = Employee::all();
        $staffJobs = (clone $base)->get();
        $staffData = $employees->map(function($emp) use ($staffJobs) {
            $myJobs = $staffJobs->where('employee_id', $emp->id);
            return [
                'employee'    => $emp,
                'total'       => $myJobs->count(),
                'completed'   => $myJobs->where('status', 'Completed')->count(),
                'in_progress' => $myJobs->where('status', 'In Progress')->count(),
                'pending'     => $myJobs->where('status', 'Pending')->count(),
                'broken'      => $myJobs->where('status', 'Broken')->count(),
                'need_assist' => $myJobs->where('need_assistant', true)->count(),
                'jobs'        => $myJobs->sortByDesc('date'),
            ];
        })->sortByDesc(fn($d) => $d['total']);
        $unassignedJobs = $staffJobs->whereNull('employee_id');

        return compact(
            'report', 'period', 'from', 'to',
            'jobs', 'jobSummary',
            'paymentJobs', 'paymentSummary', 'paymentLogs',
            'revenueJobs', 'revenueSummary',
            'statusJobs', 'statusGroups',
            'overdueJobs',
            'undeliveredJobs', 'undeliveredSummary',
            'employees', 'staffData', 'unassignedJobs'
        );
    }

    // ─────────────────────────────────────────────────────────────
    // Main view
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        return view('admin.reports.index', $this->resolveData($request));
    }

    // ─────────────────────────────────────────────────────────────
    // Excel export (SpreadsheetML — opens natively in Excel)
    // ─────────────────────────────────────────────────────────────
    public function exportExcel(Request $request)
    {
        $d = $this->resolveData($request);
        extract($d);

        $store = StoreInfo::first();
        $storeName = $store->store_name ?? 'TrackUp';
        $periodLabels = ['today'=>'Today','week'=>'This Week','month'=>'This Month','year'=>'This Year','custom'=>'Custom'];
        $periodLabel  = $periodLabels[$period] ?? 'Custom';
        $reportLabels = [
            'jobs'=>'Job Report','payment'=>'Payment Report','revenue'=>'Revenue Report',
            'status'=>'Status Report','overdue'=>'Overdue Report',
            'undelivered'=>'Undelivered Report','staff'=>'Staff Report',
        ];
        $reportLabel = $reportLabels[$report] ?? ucfirst($report);
        $filename    = strtolower(str_replace(' ', '_', $reportLabel)).'_'.date('Ymd').'.xls';

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
            xmlns:o="urn:schemas-microsoft-com:office:office"
            xmlns:x="urn:schemas-microsoft-com:office:excel"
            xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
            xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

        // Styles
        $xml .= '<Styles>
          <Style ss:ID="title"><Font ss:Bold="1" ss:Size="14"/></Style>
          <Style ss:ID="header"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#696CFF" ss:Pattern="Solid"/></Style>
          <Style ss:ID="subheader"><Font ss:Bold="1"/><Interior ss:Color="#F4F4FF" ss:Pattern="Solid"/></Style>
          <Style ss:ID="bold"><Font ss:Bold="1"/></Style>
          <Style ss:ID="money"><NumberFormat ss:Format="#,##0.00"/></Style>
          <Style ss:ID="date"><NumberFormat ss:Format="DD/MM/YYYY"/></Style>
          <Style ss:ID="altrow"><Interior ss:Color="#FAFAFF" ss:Pattern="Solid"/></Style>
        </Styles>' . "\n";

        // ── Helper closures ──
        $cell = fn($v, $type='String', $style='') =>
            '<Cell'.($style?" ss:StyleID=\"$style\"":'').'><Data ss:Type="'.$type.'">'.htmlspecialchars((string)$v, ENT_XML1).'</Data></Cell>';
        $row  = fn($cells) => '<Row>'.implode('',$cells).'</Row>';
        $hdr  = fn($cols) => '<Row>'.implode('', array_map(fn($c) => '<Cell ss:StyleID="header"><Data ss:Type="String">'.htmlspecialchars($c, ENT_XML1).'</Data></Cell>', $cols)).'</Row>';

        $sheets = [];

        // ── 1. Summary sheet ──────────────────────────────────────
        $sumRows  = '<Row><Cell ss:StyleID="title" ss:MergeAcross="1"><Data ss:Type="String">'.$storeName.' — '.$reportLabel.'</Data></Cell></Row>';
        $sumRows .= '<Row><Cell><Data ss:Type="String">Period:</Data></Cell><Cell><Data ss:Type="String">'.$periodLabel.' ('.$from.' to '.$to.')</Data></Cell></Row>';
        $sumRows .= '<Row></Row>';

        if ($report === 'jobs') {
            $sumRows .= $hdr(['Metric','Value']);
            foreach (['Total Jobs'=>$jobSummary['total'],'Completed'=>$jobSummary['completed'],
                      'In Progress'=>$jobSummary['in_progress'],'Pending'=>$jobSummary['pending'],
                      'Not Completed'=>$jobSummary['not_completed'],'Broken'=>$jobSummary['broken'],
                      'Cancelled'=>$jobSummary['cancelled']] as $k=>$v) {
                $sumRows .= $row([$cell($k), $cell($v,'Number')]);
            }
        } elseif ($report === 'payment') {
            $sumRows .= $hdr(['Metric','Value']);
            $sumRows .= $row([$cell('Total Collected (Rs.)'), $cell($paymentSummary['total_collected'],'Number','money')]);
            $sumRows .= $row([$cell('Fully Paid Orders'), $cell($paymentSummary['fully_paid_count'],'Number')]);
            $sumRows .= $row([$cell('Partial Payments'), $cell($paymentSummary['partial_count'],'Number')]);
            $sumRows .= $row([$cell('Total Transactions'), $cell($paymentSummary['transactions'],'Number')]);
        } elseif ($report === 'revenue') {
            $sumRows .= $hdr(['Metric','Value (Rs.)']);
            $sumRows .= $row([$cell('Total Billed'), $cell($revenueSummary['grand_total_billed'],'Number','money')]);
            $sumRows .= $row([$cell('Total Collected'), $cell($revenueSummary['total_collected'],'Number','money')]);
            $sumRows .= $row([$cell('Outstanding'), $cell($revenueSummary['outstanding'],'Number','money')]);
            $sumRows .= $row([$cell('Unpaid Orders'), $cell($revenueSummary['unpaid_count'],'Number')]);
        } elseif ($report === 'overdue') {
            $sumRows .= $row([$cell('Total Overdue Jobs: '.$overdueJobs->count(),'String','bold')]);
        } elseif ($report === 'undelivered') {
            $sumRows .= $hdr(['Metric','Value']);
            $sumRows .= $row([$cell('Total Undelivered'), $cell($undeliveredSummary['total'],'Number')]);
            $sumRows .= $row([$cell('Paid'), $cell($undeliveredSummary['paid'],'Number')]);
            $sumRows .= $row([$cell('Payment Outstanding'), $cell($undeliveredSummary['outstanding'],'Number')]);
            $sumRows .= $row([$cell('Amount Due (Rs.)'), $cell($undeliveredSummary['amount_due'],'Number','money')]);
        } elseif ($report === 'staff') {
            $sumRows .= $hdr(['Employee','Total','Completed','In Progress','Pending','Broken','Need Assist']);
            foreach ($staffData as $sd) {
                $sumRows .= $row([
                    $cell($sd['employee']->employee_name),
                    $cell($sd['total'],'Number'), $cell($sd['completed'],'Number'),
                    $cell($sd['in_progress'],'Number'), $cell($sd['pending'],'Number'),
                    $cell($sd['broken'],'Number'), $cell($sd['need_assist'],'Number'),
                ]);
            }
        }
        $sheets[] = ['name'=>'Summary', 'rows'=>$sumRows];

        // ── 2. Detail sheet ───────────────────────────────────────
        $detailRows = '';
        if ($report === 'jobs') {
            $detailRows .= $hdr(['#','Order No','Date','Customer','Phone','Device','Brand','Employee','Status','Amount (Rs.)']);
            foreach ($jobs as $i => $j) {
                $detailRows .= $row([
                    $cell($i+1,'Number'), $cell($j->order_no), $cell($j->date),
                    $cell($j->customer_name), $cell($j->phone_no ?? ''),
                    $cell($j->device_name), $cell($j->device_brand ?? ''),
                    $cell($j->employee?->employee_name ?? 'Unassigned'),
                    $cell($j->status), $cell($j->rupees ?? 0,'Number','money'),
                ]);
            }
            $sheets[] = ['name'=>'All Jobs', 'rows'=>$detailRows];
        } elseif ($report === 'payment') {
            $detailRows .= $hdr(['#','Date','Order No','Customer','Device','Amount Paid (Rs.)','Status']);
            foreach ($paymentLogs as $i => $log) {
                $detailRows .= $row([
                    $cell($i+1,'Number'),
                    $cell(Carbon::parse($log->paid_at)->format('d/m/Y H:i')),
                    $cell($log->jobCard->order_no ?? ''),
                    $cell($log->jobCard->customer_name ?? ''),
                    $cell($log->jobCard->device_name ?? ''),
                    $cell($log->amount,'Number','money'),
                    $cell($log->note ?? ''),
                ]);
            }
            $sheets[] = ['name'=>'Payment Logs', 'rows'=>$detailRows];
            // Job-wise sheet
            $jRows = $hdr(['#','Order No','Date','Customer','Device','Total (Rs.)','Paid (Rs.)','Balance (Rs.)','Status']);
            foreach ($paymentJobs as $i => $j) {
                $jRows .= $row([
                    $cell($i+1,'Number'), $cell($j->order_no), $cell($j->date),
                    $cell($j->customer_name), $cell($j->device_name),
                    $cell($j->rupees ?? 0,'Number','money'),
                    $cell($j->paid_amount ?? 0,'Number','money'),
                    $cell($j->balance,'Number','money'),
                    $cell($j->payment_received ? 'Paid' : ($j->paid_amount > 0 ? 'Partial' : 'Unpaid')),
                ]);
            }
            $sheets[] = ['name'=>'Jobs Detail', 'rows'=>$jRows];
        } elseif ($report === 'revenue') {
            $detailRows .= $hdr(['#','Order No','Date','Customer','Device','Billed (Rs.)','Paid (Rs.)','Balance (Rs.)','Payment Status']);
            foreach ($revenueJobs as $i => $j) {
                $detailRows .= $row([
                    $cell($i+1,'Number'), $cell($j->order_no), $cell($j->date),
                    $cell($j->customer_name), $cell($j->device_name),
                    $cell($j->grand_total,'Number','money'),
                    $cell($j->paid_amount ?? 0,'Number','money'),
                    $cell($j->balance,'Number','money'),
                    $cell($j->payment_received ? 'Paid' : ($j->paid_amount > 0 ? 'Partial' : 'Unpaid')),
                ]);
            }
            $sheets[] = ['name'=>'Revenue Detail', 'rows'=>$detailRows];
        } elseif ($report === 'status') {
            $detailRows .= $hdr(['#','Order No','Date','Customer','Device','Employee','Status','Amount (Rs.)']);
            foreach ($statusJobs as $i => $j) {
                $detailRows .= $row([
                    $cell($i+1,'Number'), $cell($j->order_no), $cell($j->date),
                    $cell($j->customer_name), $cell($j->device_name),
                    $cell($j->employee?->employee_name ?? 'Unassigned'),
                    $cell($j->status), $cell($j->rupees ?? 0,'Number','money'),
                ]);
            }
            $sheets[] = ['name'=>'Status Detail', 'rows'=>$detailRows];
        } elseif ($report === 'overdue') {
            $detailRows .= $hdr(['#','Order No','Date','Customer','Phone','Device','Employee','Status','Est. Delivery','Days Overdue']);
            foreach ($overdueJobs as $i => $j) {
                $days = Carbon::parse($j->estimated_delivery)->diffInDays(Carbon::today());
                $detailRows .= $row([
                    $cell($i+1,'Number'), $cell($j->order_no), $cell($j->date),
                    $cell($j->customer_name), $cell($j->phone_no ?? ''),
                    $cell($j->device_name),
                    $cell($j->employee?->employee_name ?? 'Unassigned'),
                    $cell($j->status), $cell($j->estimated_delivery),
                    $cell($days,'Number'),
                ]);
            }
            $sheets[] = ['name'=>'Overdue Jobs', 'rows'=>$detailRows];
        } elseif ($report === 'undelivered') {
            $detailRows .= $hdr(['#','Order No','Date','Customer','Phone','Device','Employee','Amount (Rs.)','Paid (Rs.)','Balance (Rs.)','Pay Status']);
            foreach ($undeliveredJobs as $i => $j) {
                $detailRows .= $row([
                    $cell($i+1,'Number'), $cell($j->order_no), $cell($j->date),
                    $cell($j->customer_name), $cell($j->phone_no ?? ''),
                    $cell($j->device_name),
                    $cell($j->employee?->employee_name ?? 'Unassigned'),
                    $cell($j->rupees ?? 0,'Number','money'),
                    $cell($j->paid_amount ?? 0,'Number','money'),
                    $cell($j->balance,'Number','money'),
                    $cell($j->payment_received ? 'Paid' : ($j->paid_amount > 0 ? 'Partial' : 'Unpaid')),
                ]);
            }
            $sheets[] = ['name'=>'Undelivered Jobs', 'rows'=>$detailRows];
        } elseif ($report === 'staff') {
            foreach ($staffData as $sd) {
                $empRows  = '<Row><Cell ss:StyleID="subheader" ss:MergeAcross="5"><Data ss:Type="String">'
                           .htmlspecialchars($sd['employee']->employee_name, ENT_XML1).'</Data></Cell></Row>';
                $empRows .= $hdr(['Order No','Date','Customer','Device','Status','Amount (Rs.)']);
                foreach ($sd['jobs'] as $j) {
                    $empRows .= $row([
                        $cell($j->order_no), $cell($j->date), $cell($j->customer_name),
                        $cell($j->device_name), $cell($j->status),
                        $cell($j->rupees ?? 0,'Number','money'),
                    ]);
                }
                $sheets[] = ['name'=>substr($sd['employee']->employee_name, 0, 31), 'rows'=>$empRows];
            }
        }

        // Build XML
        foreach ($sheets as $sheet) {
            $xml .= '<Worksheet ss:Name="'.htmlspecialchars($sheet['name'], ENT_XML1).'">'
                  . '<Table>'.$sheet['rows'].'</Table>'
                  . '</Worksheet>'."\n";
        }
        $xml .= '</Workbook>';

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // PDF export (dompdf)
    // ─────────────────────────────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $d = $this->resolveData($request);
        extract($d);

        $store       = StoreInfo::first();
        $storeName   = $store->store_name ?? 'TrackUp';
        $storeAddr   = $store->store_address ?? '';
        $storePhone  = $store->phone_no1 ?? '';
        $periodLabels= ['today'=>'Today','week'=>'This Week','month'=>'This Month','year'=>'This Year','custom'=>'Custom'];
        $reportLabels= [
            'jobs'=>'Job Report','payment'=>'Payment Report','revenue'=>'Revenue Report',
            'status'=>'Status Report','overdue'=>'Overdue Report',
            'undelivered'=>'Undelivered Report','staff'=>'Staff Report',
        ];
        $reportLabel = $reportLabels[$report] ?? ucfirst($report);
        $periodLabel = $periodLabels[$period] ?? 'Custom';
        $filename    = strtolower(str_replace(' ', '_', $reportLabel)).'_'.date('Ymd').'.pdf';

        $css = '
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; margin: 0; padding: 0; }
        .page { padding: 20px 24px; }
        .header { border-bottom: 2px solid #696cff; padding-bottom: 10px; margin-bottom: 14px; }
        .header h1 { font-size: 18px; margin: 0 0 2px; color: #696cff; }
        .header .meta { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th { background: #696cff; color: #fff; padding: 6px 8px; text-align: left; font-size: 9px; font-weight: bold; }
        td { padding: 5px 8px; border-bottom: 1px solid #f0f0f0; font-size: 9px; }
        tr:nth-child(even) td { background: #fafaff; }
        .num { text-align: right; }
        .section-title { font-size: 12px; font-weight: bold; color: #696cff; margin: 14px 0 6px; border-bottom: 1px solid #ebebff; padding-bottom: 4px; }
        .stat-grid { width:100%; margin-bottom:14px; }
        .stat-grid td { border:1px solid #ebebff; padding:8px 12px; }
        .stat-val { font-size:16px; font-weight:bold; color:#696cff; }
        .stat-lbl { font-size:8px; color:#888; text-transform:uppercase; letter-spacing:.05em; }
        .badge-completed { background:#d1fae5; color:#065f46; border-radius:3px; padding:1px 5px; }
        .badge-pending   { background:#fff3cd; color:#856404; border-radius:3px; padding:1px 5px; }
        .badge-progress  { background:#cff4fc; color:#055160; border-radius:3px; padding:1px 5px; }
        .badge-broken    { background:#f8d7da; color:#842029; border-radius:3px; padding:1px 5px; }
        .badge-paid      { background:#d1fae5; color:#065f46; border-radius:3px; padding:1px 5px; }
        .badge-partial   { background:#fef3c7; color:#92400e; border-radius:3px; padding:1px 5px; }
        .badge-unpaid    { background:#fee2e2; color:#991b1b; border-radius:3px; padding:1px 5px; }
        .footer { border-top:1px solid #eee; padding-top:6px; font-size:8px; color:#aaa; margin-top:10px; }
        ';

        $html  = '<html><head><style>'.$css.'</style></head><body><div class="page">';
        $html .= '<div class="header">';
        $html .= '<h1>'.$reportLabel.'</h1>';
        $html .= '<div class="meta"><strong>'.$storeName.'</strong>';
        if ($storeAddr) $html .= ' &nbsp;·&nbsp; '.$storeAddr;
        if ($storePhone) $html .= ' &nbsp;·&nbsp; '.$storePhone;
        $html .= '<br>Period: '.$periodLabel.' &nbsp;·&nbsp; '.Carbon::parse($from)->format('d M Y').' – '.Carbon::parse($to)->format('d M Y');
        $html .= ' &nbsp;·&nbsp; Generated: '.now()->format('d M Y H:i');
        $html .= '</div></div>';

        $payBadge = fn($j) => $j->payment_received
            ? '<span class="badge-paid">Paid</span>'
            : ($j->paid_amount > 0 ? '<span class="badge-partial">Partial</span>' : '<span class="badge-unpaid">Unpaid</span>');
        $stBadge = function($s) {
            $map = ['Completed'=>'completed','Pending'=>'pending','In Progress'=>'progress','Broken'=>'broken','Not Completed'=>'broken','Cancelled'=>'broken'];
            $cls = $map[$s] ?? 'pending';
            return '<span class="badge-'.$cls.'">'.htmlspecialchars($s).'</span>';
        };

        if ($report === 'jobs') {
            // Summary stats
            $html .= '<table class="stat-grid"><tr>';
            foreach (['Total'=>$jobSummary['total'],'Completed'=>$jobSummary['completed'],
                      'In Progress'=>$jobSummary['in_progress'],'Pending'=>$jobSummary['pending'],
                      'Broken'=>$jobSummary['broken'],'Cancelled'=>$jobSummary['cancelled']] as $lbl=>$val) {
                $html .= '<td><div class="stat-val">'.$val.'</div><div class="stat-lbl">'.$lbl.'</div></td>';
            }
            $html .= '</tr></table>';
            $html .= '<div class="section-title">All Jobs</div>';
            $html .= '<table><tr><th>#</th><th>Order No</th><th>Date</th><th>Customer</th><th>Device</th><th>Employee</th><th>Status</th><th class="num">Amount</th></tr>';
            foreach ($jobs as $i => $j) {
                $html .= '<tr><td>'.($i+1).'</td><td>'.$j->order_no.'</td><td>'.$j->date.'</td>'
                       . '<td>'.htmlspecialchars($j->customer_name).'</td><td>'.htmlspecialchars($j->device_name).'</td>'
                       . '<td>'.htmlspecialchars($j->employee?->employee_name ?? '—').'</td>'
                       . '<td>'.$stBadge($j->status).'</td>'
                       . '<td class="num">Rs. '.number_format($j->rupees ?? 0).'</td></tr>';
            }
            $html .= '</table>';

        } elseif ($report === 'payment') {
            $html .= '<table class="stat-grid"><tr>';
            $html .= '<td><div class="stat-val">Rs. '.number_format($paymentSummary['total_collected']).'</div><div class="stat-lbl">Total Collected</div></td>';
            $html .= '<td><div class="stat-val">'.$paymentSummary['fully_paid_count'].'</div><div class="stat-lbl">Fully Paid</div></td>';
            $html .= '<td><div class="stat-val">'.$paymentSummary['partial_count'].'</div><div class="stat-lbl">Partial</div></td>';
            $html .= '<td><div class="stat-val">'.$paymentSummary['transactions'].'</div><div class="stat-lbl">Transactions</div></td>';
            $html .= '</tr></table>';
            $html .= '<div class="section-title">Payment Logs</div>';
            $html .= '<table><tr><th>#</th><th>Date & Time</th><th>Order No</th><th>Customer</th><th>Device</th><th class="num">Amount (Rs.)</th><th>Note</th></tr>';
            foreach ($paymentLogs as $i => $log) {
                $html .= '<tr><td>'.($i+1).'</td><td>'.Carbon::parse($log->paid_at)->format('d/m/Y H:i').'</td>'
                       . '<td>'.htmlspecialchars($log->jobCard->order_no ?? '').'</td>'
                       . '<td>'.htmlspecialchars($log->jobCard->customer_name ?? '').'</td>'
                       . '<td>'.htmlspecialchars($log->jobCard->device_name ?? '').'</td>'
                       . '<td class="num">'.number_format($log->amount, 2).'</td>'
                       . '<td>'.htmlspecialchars($log->note ?? '').'</td></tr>';
            }
            $html .= '</table>';
            $html .= '<div class="section-title">Jobs Detail</div>';
            $html .= '<table><tr><th>#</th><th>Order No</th><th>Date</th><th>Customer</th><th>Device</th><th class="num">Total</th><th class="num">Paid</th><th class="num">Balance</th><th>Status</th></tr>';
            foreach ($paymentJobs as $i => $j) {
                $html .= '<tr><td>'.($i+1).'</td><td>'.$j->order_no.'</td><td>'.$j->date.'</td>'
                       . '<td>'.htmlspecialchars($j->customer_name).'</td><td>'.htmlspecialchars($j->device_name).'</td>'
                       . '<td class="num">'.number_format($j->rupees ?? 0).'</td>'
                       . '<td class="num">'.number_format($j->paid_amount ?? 0).'</td>'
                       . '<td class="num">'.number_format($j->balance).'</td>'
                       . '<td>'.$payBadge($j).'</td></tr>';
            }
            $html .= '</table>';

        } elseif ($report === 'revenue') {
            $html .= '<table class="stat-grid"><tr>';
            $html .= '<td><div class="stat-val">Rs. '.number_format($revenueSummary['grand_total_billed']).'</div><div class="stat-lbl">Total Billed</div></td>';
            $html .= '<td><div class="stat-val">Rs. '.number_format($revenueSummary['total_collected']).'</div><div class="stat-lbl">Collected</div></td>';
            $html .= '<td><div class="stat-val">Rs. '.number_format($revenueSummary['outstanding']).'</div><div class="stat-lbl">Outstanding</div></td>';
            $html .= '<td><div class="stat-val">'.$revenueSummary['unpaid_count'].'</div><div class="stat-lbl">Unpaid Orders</div></td>';
            $html .= '</tr></table>';
            $html .= '<div class="section-title">Revenue Detail</div>';
            $html .= '<table><tr><th>#</th><th>Order No</th><th>Date</th><th>Customer</th><th>Device</th><th class="num">Billed</th><th class="num">Paid</th><th class="num">Balance</th><th>Status</th></tr>';
            foreach ($revenueJobs as $i => $j) {
                $html .= '<tr><td>'.($i+1).'</td><td>'.$j->order_no.'</td><td>'.$j->date.'</td>'
                       . '<td>'.htmlspecialchars($j->customer_name).'</td><td>'.htmlspecialchars($j->device_name).'</td>'
                       . '<td class="num">'.number_format($j->grand_total).'</td>'
                       . '<td class="num">'.number_format($j->paid_amount ?? 0).'</td>'
                       . '<td class="num">'.number_format($j->balance).'</td>'
                       . '<td>'.$payBadge($j).'</td></tr>';
            }
            $html .= '</table>';

        } elseif ($report === 'status') {
            foreach ($statusGroups as $status => $group) {
                $html .= '<div class="section-title">'.htmlspecialchars($status).' ('.$group->count().')</div>';
                $html .= '<table><tr><th>#</th><th>Order No</th><th>Date</th><th>Customer</th><th>Device</th><th>Employee</th><th class="num">Amount</th></tr>';
                foreach ($group as $i => $j) {
                    $html .= '<tr><td>'.($i+1).'</td><td>'.$j->order_no.'</td><td>'.$j->date.'</td>'
                           . '<td>'.htmlspecialchars($j->customer_name).'</td><td>'.htmlspecialchars($j->device_name).'</td>'
                           . '<td>'.htmlspecialchars($j->employee?->employee_name ?? '—').'</td>'
                           . '<td class="num">Rs. '.number_format($j->rupees ?? 0).'</td></tr>';
                }
                $html .= '</table>';
            }

        } elseif ($report === 'overdue') {
            $html .= '<p style="font-size:11px;font-weight:bold;color:#e55;">⚠ '.$overdueJobs->count().' overdue jobs</p>';
            $html .= '<table><tr><th>#</th><th>Order No</th><th>Date</th><th>Customer</th><th>Phone</th><th>Device</th><th>Employee</th><th>Status</th><th>Est. Delivery</th><th class="num">Days Late</th></tr>';
            foreach ($overdueJobs as $i => $j) {
                $days = Carbon::parse($j->estimated_delivery)->diffInDays(Carbon::today());
                $html .= '<tr><td>'.($i+1).'</td><td>'.$j->order_no.'</td><td>'.$j->date.'</td>'
                       . '<td>'.htmlspecialchars($j->customer_name).'</td><td>'.htmlspecialchars($j->phone_no ?? '').'</td>'
                       . '<td>'.htmlspecialchars($j->device_name).'</td>'
                       . '<td>'.htmlspecialchars($j->employee?->employee_name ?? '—').'</td>'
                       . '<td>'.$stBadge($j->status).'</td>'
                       . '<td>'.$j->estimated_delivery.'</td><td class="num" style="color:#e55;font-weight:bold;">'.$days.'</td></tr>';
            }
            $html .= '</table>';

        } elseif ($report === 'undelivered') {
            $html .= '<table class="stat-grid"><tr>';
            $html .= '<td><div class="stat-val">'.$undeliveredSummary['total'].'</div><div class="stat-lbl">Total</div></td>';
            $html .= '<td><div class="stat-val">'.$undeliveredSummary['paid'].'</div><div class="stat-lbl">Paid</div></td>';
            $html .= '<td><div class="stat-val">'.$undeliveredSummary['outstanding'].'</div><div class="stat-lbl">Outstanding</div></td>';
            $html .= '<td><div class="stat-val">Rs. '.number_format($undeliveredSummary['amount_due']).'</div><div class="stat-lbl">Amount Due</div></td>';
            $html .= '</tr></table>';
            $html .= '<table><tr><th>#</th><th>Order No</th><th>Date</th><th>Customer</th><th>Phone</th><th>Device</th><th>Employee</th><th class="num">Total</th><th class="num">Paid</th><th class="num">Balance</th><th>Pay Status</th></tr>';
            foreach ($undeliveredJobs as $i => $j) {
                $html .= '<tr><td>'.($i+1).'</td><td>'.$j->order_no.'</td><td>'.$j->date.'</td>'
                       . '<td>'.htmlspecialchars($j->customer_name).'</td><td>'.htmlspecialchars($j->phone_no ?? '').'</td>'
                       . '<td>'.htmlspecialchars($j->device_name).'</td>'
                       . '<td>'.htmlspecialchars($j->employee?->employee_name ?? '—').'</td>'
                       . '<td class="num">'.number_format($j->rupees ?? 0).'</td>'
                       . '<td class="num">'.number_format($j->paid_amount ?? 0).'</td>'
                       . '<td class="num">'.number_format($j->balance).'</td>'
                       . '<td>'.$payBadge($j).'</td></tr>';
            }
            $html .= '</table>';

        } elseif ($report === 'staff') {
            foreach ($staffData as $sd) {
                if ($sd['total'] === 0) continue;
                $html .= '<div class="section-title">'.htmlspecialchars($sd['employee']->employee_name)
                        .' — '.$sd['total'].' jobs</div>';
                $html .= '<table class="stat-grid"><tr>';
                $html .= '<td><div class="stat-val">'.$sd['completed'].'</div><div class="stat-lbl">Completed</div></td>';
                $html .= '<td><div class="stat-val">'.$sd['in_progress'].'</div><div class="stat-lbl">In Progress</div></td>';
                $html .= '<td><div class="stat-val">'.$sd['pending'].'</div><div class="stat-lbl">Pending</div></td>';
                $html .= '<td><div class="stat-val">'.$sd['broken'].'</div><div class="stat-lbl">Broken</div></td>';
                $html .= '</tr></table>';
                $html .= '<table><tr><th>Order No</th><th>Date</th><th>Customer</th><th>Device</th><th>Status</th><th class="num">Amount</th></tr>';
                foreach ($sd['jobs'] as $j) {
                    $html .= '<tr><td>'.$j->order_no.'</td><td>'.$j->date.'</td>'
                           . '<td>'.htmlspecialchars($j->customer_name).'</td><td>'.htmlspecialchars($j->device_name).'</td>'
                           . '<td>'.$stBadge($j->status).'</td>'
                           . '<td class="num">Rs. '.number_format($j->rupees ?? 0).'</td></tr>';
                }
                $html .= '</table>';
            }
        }

        $html .= '<div class="footer">Generated by '.$storeName.' · '.now()->format('d M Y H:i').' · TrackUp</div>';
        $html .= '</div></body></html>';

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}

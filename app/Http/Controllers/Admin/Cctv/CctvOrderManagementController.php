<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvLead;
use App\Models\CctvSurvey;
use App\Models\CctvQuotation;
use App\Models\CctvProject;
use App\Models\CctvInvoice;
use Illuminate\Http\Request;

class CctvOrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim($request->get('q', ''));
        $results = collect();

        if ($q !== '') {
            // Search across all pipeline entities by customer name, mobile, or reference number
            $leads = CctvLead::where(function ($query) use ($q) {
                $query->where('customer_name', 'like', "%{$q}%")
                      ->orWhere('mobile', 'like', "%{$q}%")
                      ->orWhere('lead_no', 'like', "%{$q}%");
            })->with(['surveys', 'quotations'])->latest()->get();

            $surveys = CctvSurvey::where(function ($query) use ($q) {
                $query->where('customer_name', 'like', "%{$q}%")
                      ->orWhere('mobile', 'like', "%{$q}%")
                      ->orWhere('survey_no', 'like', "%{$q}%");
            })->whereNull('lead_id')->latest()->get(); // orphan surveys (no lead)

            $quotations = CctvQuotation::where(function ($query) use ($q) {
                $query->where('customer_name', 'like', "%{$q}%")
                      ->orWhere('mobile', 'like', "%{$q}%")
                      ->orWhere('quote_no', 'like', "%{$q}%");
            })->whereNull('lead_id')->latest()->get(); // orphan quotations

            $projects = CctvProject::where(function ($query) use ($q) {
                $query->where('customer_name', 'like', "%{$q}%")
                      ->orWhere('mobile', 'like', "%{$q}%")
                      ->orWhere('project_no', 'like', "%{$q}%");
            })->whereNull('lead_id')->latest()->get(); // orphan projects

            $invoices = CctvInvoice::where(function ($query) use ($q) {
                $query->where('customer_name', 'like', "%{$q}%")
                      ->orWhere('mobile', 'like', "%{$q}%")
                      ->orWhere('invoice_no', 'like', "%{$q}%");
            })->whereNull('lead_id')->latest()->get(); // orphan invoices

            // Build unified result rows — each "order" anchored to a lead
            foreach ($leads as $lead) {
                $latestSurvey    = $lead->surveys->sortByDesc('id')->first();
                $latestQuotation = $lead->quotations->sortByDesc('id')->first();
                $project = CctvProject::where('lead_id', $lead->id)->latest()->first();
                $invoice = $project
                    ? CctvInvoice::where('project_id', $project->id)->latest()->first()
                    : CctvInvoice::where('lead_id', $lead->id)->latest()->first();

                $results->push([
                    'type'       => 'lead',
                    'ref'        => $lead->lead_no,
                    'customer'   => $lead->customer_name,
                    'mobile'     => $lead->mobile,
                    'status'     => $lead->status,
                    'lead'       => $lead,
                    'survey'     => $latestSurvey,
                    'quotation'  => $latestQuotation,
                    'project'    => $project,
                    'invoice'    => $invoice,
                    'updated_at' => $lead->updated_at,
                ]);
            }

            // Orphan surveys (no lead)
            foreach ($surveys as $s) {
                $results->push([
                    'type'       => 'survey',
                    'ref'        => $s->survey_no,
                    'customer'   => $s->customer_name,
                    'mobile'     => $s->mobile,
                    'status'     => $s->status ?? '—',
                    'lead'       => null,
                    'survey'     => $s,
                    'quotation'  => null,
                    'project'    => null,
                    'invoice'    => null,
                    'updated_at' => $s->updated_at,
                ]);
            }

            // Orphan quotations
            foreach ($quotations as $qt) {
                $results->push([
                    'type'       => 'quotation',
                    'ref'        => $qt->quote_no,
                    'customer'   => $qt->customer_name,
                    'mobile'     => $qt->mobile,
                    'status'     => $qt->status ?? '—',
                    'lead'       => null,
                    'survey'     => null,
                    'quotation'  => $qt,
                    'project'    => null,
                    'invoice'    => null,
                    'updated_at' => $qt->updated_at,
                ]);
            }

            // Orphan projects
            foreach ($projects as $prj) {
                $invoice = CctvInvoice::where('project_id', $prj->id)->latest()->first();
                $results->push([
                    'type'       => 'project',
                    'ref'        => $prj->project_no,
                    'customer'   => $prj->customer_name,
                    'mobile'     => $prj->mobile,
                    'status'     => $prj->status ?? '—',
                    'lead'       => null,
                    'survey'     => null,
                    'quotation'  => null,
                    'project'    => $prj,
                    'invoice'    => $invoice,
                    'updated_at' => $prj->updated_at,
                ]);
            }

            // Orphan invoices
            foreach ($invoices as $inv) {
                $results->push([
                    'type'       => 'invoice',
                    'ref'        => $inv->invoice_no,
                    'customer'   => $inv->customer_name,
                    'mobile'     => $inv->mobile,
                    'status'     => $inv->payment_status ?? '—',
                    'lead'       => null,
                    'survey'     => null,
                    'quotation'  => null,
                    'project'    => null,
                    'invoice'    => $inv,
                    'updated_at' => $inv->updated_at,
                ]);
            }

            $results = $results->sortByDesc('updated_at')->values();
        }

        return view('admin.cctv.order-management.index', compact('q', 'results'));
    }
}

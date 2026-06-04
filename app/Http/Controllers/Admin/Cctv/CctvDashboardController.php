<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvLead;
use App\Models\CctvProject;
use App\Models\CctvServiceTicket;
use App\Models\CctvAmcContract;
use App\Models\CctvRepair;
use App\Models\CctvAsset;
use App\Models\CctvInventory;

class CctvDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'leads_new'        => CctvLead::where('status','New Lead')->count(),
            'leads_total'      => CctvLead::count(),
            'projects_active'  => CctvProject::whereNotIn('stage',['Customer Handover','Warranty Activated'])->count(),
            'projects_total'   => CctvProject::count(),
            'tickets_open'     => CctvServiceTicket::whereIn('status',['Open','Assigned','In Progress','Waiting Parts'])->count(),
            'tickets_completed'=> CctvServiceTicket::where('status','Completed')->count(),
            'amc_active'       => CctvAmcContract::where('status','Active')->count(),
            'amc_renewal_due'  => CctvAmcContract::where('status','Active')
                                    ->whereDate('end_date','<=',now()->addDays(30))->count(),
            'repairs_pending'  => CctvRepair::whereNotIn('status',['Collected','Cancelled'])->count(),
            'assets_total'     => CctvAsset::where('status','Active')->count(),
            'assets_faulty'    => CctvAsset::where('status','Faulty')->count(),
            'low_stock'        => CctvInventory::whereRaw('qty_in_stock <= low_stock_alert')->count(),
            'warranty_expiring'=> CctvAsset::where('status','Active')
                                    ->whereNotNull('warranty_expiry')
                                    ->whereDate('warranty_expiry','<=',now()->addDays(30))
                                    ->whereDate('warranty_expiry','>=',now())
                                    ->count(),
        ];

        $recentLeads   = CctvLead::latest()->limit(5)->get();
        $recentTickets = CctvServiceTicket::with('technician')->latest()->limit(5)->get();
        $recentProjects= CctvProject::latest()->limit(5)->get();
        $upcomingAmc   = CctvAmcContract::where('status','Active')
                            ->whereDate('end_date','<=',now()->addDays(60))
                            ->orderBy('end_date')->limit(5)->get();

        return view('admin.cctv.dashboard', compact('stats','recentLeads','recentTickets','recentProjects','upcomingAmc'));
    }
}

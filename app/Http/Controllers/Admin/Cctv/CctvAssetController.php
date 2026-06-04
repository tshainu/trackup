<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvAsset;
use App\Models\CctvProject;
use App\Models\Customer;
use Illuminate\Http\Request;

class CctvAssetController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'all');
        $search = $request->get('q');
        $type   = $request->get('type');
        $query  = CctvAsset::latest();

        if ($tab !== 'all') {
            $map = ['active'=>'Active','faulty'=>'Faulty','replaced'=>'Replaced','removed'=>'Removed'];
            if (isset($map[$tab])) $query->where('status', $map[$tab]);
        }
        if ($type) $query->where('asset_type', $type);
        if ($search) {
            $query->where(fn($q) => $q->where('customer_name','like',"%$search%")
                ->orWhere('asset_id','like',"%$search%")
                ->orWhere('serial_number','like',"%$search%")
                ->orWhere('brand','like',"%$search%"));
        }

        $assets = $query->paginate(20)->withQueryString();
        $counts = [
            'all'      => CctvAsset::count(),
            'active'   => CctvAsset::where('status','Active')->count(),
            'faulty'   => CctvAsset::where('status','Faulty')->count(),
            'replaced' => CctvAsset::where('status','Replaced')->count(),
        ];
        $expiringWarranty = CctvAsset::where('status','Active')
            ->whereNotNull('warranty_expiry')
            ->whereDate('warranty_expiry','<=', now()->addDays(30))
            ->whereDate('warranty_expiry','>=', now())
            ->count();

        return view('admin.cctv.assets.index', compact('assets','tab','search','counts','type','expiringWarranty'));
    }

    public function create(Request $request)
    {
        $projects  = CctvProject::orderBy('customer_name')->get();
        $projectId = $request->get('project_id');
        $project   = $projectId ? CctvProject::find($projectId) : null;
        return view('admin.cctv.assets.create', compact('projects','project'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_type'        => 'required|in:Camera,DVR,NVR,HDD,Switch,UPS,Router,Other',
            'customer_name'     => 'nullable|string|max:150',
            'installation_date' => 'nullable|date',
            'warranty_expiry'   => 'nullable|date',
        ]);

        CctvAsset::create([
            'asset_id'          => CctvAsset::nextAssetId(),
            'project_id'        => $request->project_id,
            'customer_id'       => $request->customer_id,
            'customer_name'     => $request->customer_name,
            'asset_type'        => $request->asset_type,
            'serial_number'     => $request->serial_number,
            'brand'             => $request->brand,
            'model'             => $request->model,
            'installation_date' => $request->installation_date,
            'warranty_expiry'   => $request->warranty_expiry,
            'location'          => $request->location,
            'status'            => 'Active',
            'notes'             => $request->notes,
        ]);

        return redirect()->route('admin.cctv.assets.index')->with('success', 'Asset registered.');
    }

    public function show(CctvAsset $asset)
    {
        $asset->load('project');
        return view('admin.cctv.assets.show', compact('asset'));
    }

    public function edit(CctvAsset $asset)
    {
        $projects = CctvProject::orderBy('customer_name')->get();
        return view('admin.cctv.assets.edit', compact('asset','projects'));
    }

    public function update(Request $request, CctvAsset $asset)
    {
        $request->validate([
            'asset_type' => 'required|in:Camera,DVR,NVR,HDD,Switch,UPS,Router,Other',
            'status'     => 'required|in:Active,Faulty,Replaced,Removed',
        ]);
        $asset->update($request->only(['asset_type','serial_number','brand','model','installation_date','warranty_expiry','location','status','notes','customer_name']));
        return redirect()->route('admin.cctv.assets.show', $asset)->with('success', 'Asset updated.');
    }

    public function destroy(CctvAsset $asset)
    {
        $asset->delete();
        return redirect()->route('admin.cctv.assets.index')->with('success', 'Asset removed.');
    }
}

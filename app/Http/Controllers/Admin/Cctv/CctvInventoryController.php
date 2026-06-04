<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvInventory;
use App\Models\CctvInventoryLog;
use Illuminate\Http\Request;

class CctvInventoryController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->get('q');
        $category = $request->get('category');
        $query    = CctvInventory::latest();

        if ($category) $query->where('category', $category);
        if ($search) {
            $query->where(fn($q) => $q->where('name','like',"%$search%")
                ->orWhere('item_code','like',"%$search%")
                ->orWhere('brand','like',"%$search%"));
        }

        $items     = $query->paginate(20)->withQueryString();
        $lowStock  = CctvInventory::whereRaw('qty_in_stock <= low_stock_alert')->count();
        $totalValue= CctvInventory::selectRaw('SUM(qty_in_stock * unit_cost) as val')->value('val') ?? 0;
        $categories= ['Camera','DVR','NVR','HDD','Cable','Connector','Power Supply','UPS','Switch','Router','Other'];

        return view('admin.cctv.inventory.index', compact('items','search','category','lowStock','totalValue','categories'));
    }

    public function create()
    {
        $categories = ['Camera','DVR','NVR','HDD','Cable','Connector','Power Supply','UPS','Switch','Router','Other'];
        return view('admin.cctv.inventory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:200',
            'category' => 'required',
            'qty_in_stock' => 'required|integer|min:0',
        ]);

        $item = CctvInventory::create([
            'item_code'       => $request->item_code,
            'name'            => $request->name,
            'category'        => $request->category,
            'brand'           => $request->brand,
            'model'           => $request->model,
            'qty_in_stock'    => $request->qty_in_stock,
            'low_stock_alert' => $request->low_stock_alert ?? 5,
            'unit_cost'       => $request->unit_cost ?? 0,
            'selling_price'   => $request->selling_price ?? 0,
            'notes'           => $request->notes,
        ]);

        if ($request->qty_in_stock > 0) {
            CctvInventoryLog::create([
                'inventory_id' => $item->id,
                'type'         => 'in',
                'qty'          => $request->qty_in_stock,
                'note'         => 'Initial stock',
                'unit_cost'    => $request->unit_cost,
            ]);
        }

        return redirect()->route('admin.cctv.inventory.index')->with('success', "Item '{$item->name}' added.");
    }

    public function show(CctvInventory $inventory)
    {
        $inventory->load('logs');
        return view('admin.cctv.inventory.show', compact('inventory'));
    }

    public function edit(CctvInventory $inventory)
    {
        $categories = ['Camera','DVR','NVR','HDD','Cable','Connector','Power Supply','UPS','Switch','Router','Other'];
        return view('admin.cctv.inventory.edit', compact('inventory','categories'));
    }

    public function update(Request $request, CctvInventory $inventory)
    {
        $request->validate(['name' => 'required|string|max:200', 'category' => 'required']);
        $inventory->update($request->only(['item_code','name','category','brand','model','low_stock_alert','unit_cost','selling_price','notes']));
        return redirect()->route('admin.cctv.inventory.show', $inventory)->with('success', 'Item updated.');
    }

    public function stockIn(Request $request, CctvInventory $inventory)
    {
        $request->validate(['qty' => 'required|integer|min:1']);
        $inventory->increment('qty_in_stock', $request->qty);
        CctvInventoryLog::create([
            'inventory_id' => $inventory->id,
            'type'         => 'in',
            'qty'          => $request->qty,
            'reference'    => $request->reference,
            'note'         => $request->note ?? 'Stock In',
            'unit_cost'    => $request->unit_cost,
        ]);
        return back()->with('success', "Added {$request->qty} units.");
    }

    public function stockOut(Request $request, CctvInventory $inventory)
    {
        $request->validate(['qty' => 'required|integer|min:1']);
        if ($inventory->qty_in_stock < $request->qty) {
            return back()->with('error', 'Insufficient stock.');
        }
        $inventory->decrement('qty_in_stock', $request->qty);
        CctvInventoryLog::create([
            'inventory_id' => $inventory->id,
            'type'         => 'out',
            'qty'          => $request->qty,
            'reference'    => $request->reference,
            'note'         => $request->note ?? 'Stock Out',
        ]);
        return back()->with('success', "Removed {$request->qty} units.");
    }

    public function destroy(CctvInventory $inventory)
    {
        $name = $inventory->name;
        $inventory->delete();
        return redirect()->route('admin.cctv.inventory.index')->with('success', "'{$name}' deleted.");
    }
}

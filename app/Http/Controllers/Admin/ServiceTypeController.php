<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $types = ServiceType::orderBy('name')->get();
        return view('admin.service-types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'icon'        => 'nullable|string|max:50',
            'base_charge' => 'nullable|numeric|min:0',
        ]);

        ServiceType::create([
            'name'        => $request->name,
            'icon'        => $request->input('icon', 'bx-wrench'),
            'base_charge' => $request->input('base_charge', 0),
            'active'      => true,
        ]);

        return back()->with('success', "Service type '{$request->name}' added.");
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'icon'        => 'nullable|string|max:50',
            'base_charge' => 'nullable|numeric|min:0',
            'active'      => 'nullable|boolean',
        ]);

        $serviceType->update([
            'name'        => $request->name,
            'icon'        => $request->input('icon', $serviceType->icon),
            'base_charge' => $request->input('base_charge', 0),
            'active'      => $request->has('active') ? (bool)$request->active : $serviceType->active,
        ]);

        return back()->with('success', "Service type updated.");
    }

    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->complaints()->count() > 0) {
            return back()->with('error', "Cannot delete — complaints are linked to this service type.");
        }
        $serviceType->delete();
        return back()->with('success', "Service type deleted.");
    }

    public function toggle(ServiceType $serviceType)
    {
        $serviceType->update(['active' => !$serviceType->active]);
        return back()->with('success', $serviceType->name . ($serviceType->active ? ' activated.' : ' deactivated.'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::orderBy('name')->get();
        return view('admin.service-types.index', compact('serviceTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:50',
            'base_charge' => 'nullable|numeric|min:0',
            'milestones'  => 'nullable|string',
        ]);

        $milestones = null;
        if ($request->filled('milestones')) {
            $decoded = json_decode($request->milestones, true);
            $milestones = is_array($decoded) ? $decoded : null;
        }

        ServiceType::create([
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $request->input('icon', 'bx-wrench'),
            'base_charge' => $request->input('base_charge', 0),
            'active'      => true,
            'milestones'  => $milestones,
        ]);

        return back()->with('success', "Service type '{$request->name}' added.");
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:50',
            'base_charge' => 'nullable|numeric|min:0',
            'active'      => 'nullable|boolean',
            'milestones'  => 'nullable|string',
        ]);

        $milestones = $serviceType->milestones;
        if ($request->has('milestones')) {
            $decoded = json_decode($request->milestones, true);
            $milestones = is_array($decoded) ? $decoded : null;
        }

        $serviceType->update([
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $request->input('icon', $serviceType->icon),
            'base_charge' => $request->input('base_charge', 0),
            'active'      => $request->has('active') ? (bool)$request->active : $serviceType->active,
            'milestones'  => $milestones,
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

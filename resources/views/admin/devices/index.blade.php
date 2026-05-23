@extends('layouts.admin')

@section('title', 'Device Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Device Management</h2>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    {{-- Add Device Type --}}
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <span class="card-header-title"><i class="ti ti-device-laptop me-1"></i> Add Device Type</span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.devices.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Device Type Name</label>
                        <input type="text" name="device_name" class="form-control @error('device_name') is-invalid @enderror"
                               value="{{ old('device_name') }}" placeholder="e.g. Laptop, Tablet" required>
                        @error('device_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Device Type</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Device Types List --}}
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-header-title"><i class="ti ti-list me-1"></i> Device Types</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Device Type</th>
                                <th>Brands</th>
                                <th>Faults</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devices as $device)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $device->device_name }}</strong></td>
                                <td>
                                    @if($device->brands->count())
                                        @foreach($device->brands as $brand)
                                            <span class="badge bg-label-info me-1 mb-1">
                                                {{ $brand->device_brand }}
                                                <form action="{{ route('admin.devices.brands.destroy', $brand->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-close btn-close-white" style="font-size:0.5rem;" onclick="return confirm('Remove brand?')"></button>
                                                </form>
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if($device->faults->count())
                                        @foreach($device->faults as $fault)
                                            <span class="badge bg-label-warning me-1 mb-1">
                                                {{ $fault->device_fault }}
                                                <form action="{{ route('admin.devices.faults.destroy', $fault->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-close" style="font-size:0.5rem;" onclick="return confirm('Remove fault?')"></button>
                                                </form>
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">None</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-success mb-1"
                                            data-bs-toggle="modal" data-bs-target="#addBrandModal{{ $device->id }}">+ Brand</button>
                                    <button class="btn btn-sm btn-outline-warning mb-1"
                                            data-bs-toggle="modal" data-bs-target="#addFaultModal{{ $device->id }}">+ Fault</button>
                                    <form action="{{ route('admin.devices.destroy', $device->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger mb-1"
                                                onclick="return confirm('Delete device type and all its brands/faults?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No device types yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Brand/Fault Modals (outside table) --}}
@foreach($devices as $device)
{{-- Add Brand Modal --}}
<div class="modal fade" id="addBrandModal{{ $device->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Brand to {{ $device->device_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.devices.brands.store') }}" method="POST">
                @csrf
                <input type="hidden" name="device_list_id" value="{{ $device->id }}">
                <div class="modal-body">
                    <label class="form-label">Brand Name</label>
                    <input type="text" name="device_brand" class="form-control" placeholder="e.g. Dell, HP, Apple" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Fault Modal --}}
<div class="modal fade" id="addFaultModal{{ $device->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Fault to {{ $device->device_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.devices.faults.store') }}" method="POST">
                @csrf
                <input type="hidden" name="device_list_id" value="{{ $device->id }}">
                <div class="modal-body">
                    <label class="form-label">Fault/Issue Name</label>
                    <input type="text" name="device_fault" class="form-control" placeholder="e.g. Screen Cracked, Not Charging" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Add Fault</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

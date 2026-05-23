@extends('layouts.admin')

@section('title', 'Store Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Store Settings</h2>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Business Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.store.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Store Name <span class="text-danger">*</span></label>
                        <input type="text" name="store_name" class="form-control @error('store_name') is-invalid @enderror"
                               value="{{ old('store_name', $store->store_name ?? '') }}" required>
                        @error('store_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Registration No.</label>
                        <input type="text" name="registration_no" class="form-control"
                               value="{{ old('registration_no', $store->registration_no ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Store Address</label>
                        <textarea name="store_address" class="form-control" rows="3">{{ old('store_address', $store->store_address ?? '') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Phone 1</label>
                            <input type="text" name="phone_no1" class="form-control"
                                   value="{{ old('phone_no1', $store->phone_no1 ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Phone 2</label>
                            <input type="text" name="phone_no2" class="form-control"
                                   value="{{ old('phone_no2', $store->phone_no2 ?? '') }}">
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-muted mb-3">Owner Information</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Owner Name</label>
                        <input type="text" name="owner_name" class="form-control"
                               value="{{ old('owner_name', $store->owner_name ?? '') }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Owner Phone</label>
                            <input type="text" name="owner_phoneno" class="form-control"
                                   value="{{ old('owner_phoneno', $store->owner_phoneno ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Owner Address</label>
                            <input type="text" name="owner_address" class="form-control"
                                   value="{{ old('owner_address', $store->owner_address ?? '') }}">
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Current Info</h5>
            </div>
            <div class="card-body">
                @if($store->store_name ?? false)
                    <p><strong>Name:</strong> {{ $store->store_name }}</p>
                    <p><strong>Reg No:</strong> {{ $store->registration_no ?? '—' }}</p>
                    <p><strong>Phone:</strong> {{ $store->phone_no1 ?? '—' }}</p>
                    <p><strong>Owner:</strong> {{ $store->owner_name ?? '—' }}</p>
                @else
                    <p class="text-muted">No store info set yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

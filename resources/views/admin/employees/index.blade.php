@extends('layouts.admin')
@section('title', 'Employees')
@section('page-title', 'Employees')
@section('breadcrumb')<li class="breadcrumb-item active">Employees</li>@endsection

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center py-3">
    <span><i class='bx bx-group me-1'></i> Employee List</span>
    <a href="{{ route('admin.employees.create') }}" class="btn btn-sm" style="background:#7c4dff;color:#fff"><i class='bx bx-plus'></i> Add Employee</a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover" id="empTable">
        <thead>
          <tr>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Role</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Username</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($employees as $emp)
          <tr>
            <td><span class="fw-semibold text-primary">{{ $emp->user_id }}</span></td>
            <td>
              <div class="fw-semibold">{{ $emp->employee_name }}</div>
              <small class="text-muted">{{ $emp->nic }}</small>
            </td>
            <td><span class="badge bg-light text-dark">{{ $emp->role }}</span></td>
            <td>{{ $emp->phone_no_1 }}</td>
            <td>{{ $emp->email ?: '—' }}</td>
            <td><code>{{ $emp->user_name }}</code></td>
            <td>
              @if($emp->status === 'active')
                <span class="badge bg-success">Active</span>
              @else
                <span class="badge bg-danger">Inactive</span>
              @endif
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('admin.employees.edit', $emp) }}" class="btn btn-sm btn-outline-secondary py-0"><i class='bx bx-edit'></i></a>
                <form action="{{ route('admin.employees.destroy', $emp) }}" method="POST" onsubmit="return confirm('Remove employee?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger py-0"><i class='bx bx-trash'></i></button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    {{ $employees->links() }}
  </div>
</div>
@endsection
@push('scripts')
<script>$('#empTable').DataTable({ paging:false, info:false, searching:true, order:[] });</script>
@endpush

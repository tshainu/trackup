@extends('layouts.admin')
@section('title', 'Service Types')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Service Types</h1>
            <p class="text-sm text-gray-500">Manage field service categories and base charges</p>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Type
        </button>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if($serviceTypes->isEmpty())
        <div class="text-center py-12 text-gray-400 text-sm">No service types yet</div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Base Charge</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($serviceTypes as $st)
                <tr class="hover:bg-gray-50 transition {{ $st->active ? '' : 'opacity-50' }}">
                    <td class="px-4 py-3 font-semibold text-gray-800">{{ $st->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $st->description ?: '—' }}</td>
                    <td class="px-4 py-3 text-right font-mono font-semibold text-gray-800">Rs. {{ number_format($st->base_charge,2) }}</td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('admin.service-types.toggle', $st) }}">
                            @csrf @method('PATCH')
                            <button class="px-2 py-0.5 rounded-full text-xs font-medium {{ $st->active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                                {{ $st->active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="openEdit({{ $st->id }}, '{{ addslashes($st->name) }}', {{ $st->base_charge }}, '{{ addslashes($st->description ?? '') }}')"
                                class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition mr-1">Edit</button>
                        <form method="POST" action="{{ route('admin.service-types.destroy', $st) }}" class="inline"
                              onsubmit="return confirm('Delete {{ $st->name }}?')">
                            @csrf @method('DELETE')
                            <button class="text-xs px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition">Del</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

{{-- Add Modal --}}
<div id="addModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h3 class="font-bold text-gray-900">Add Service Type</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.service-types.store') }}" class="p-5 space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. AC Service, RO Repair"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                <input type="text" name="description" placeholder="Optional short description"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Base Charge (Rs.) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="base_charge" required placeholder="0.00"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')"
                        class="flex-1 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">Cancel</button>
                <button class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">Add</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h3 class="font-bold text-gray-900">Edit Service Type</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editForm" method="POST" class="p-5 space-y-3">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" id="editName" name="name" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                <input type="text" id="editDescription" name="description"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Base Charge (Rs.)</label>
                <input type="number" step="0.01" min="0" id="editCharge" name="base_charge" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                        class="flex-1 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">Cancel</button>
                <button class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">Save</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEdit(id, name, charge, description) {
    document.getElementById('editForm').action = `/admin/service-types/${id}`;
    document.getElementById('editName').value        = name;
    document.getElementById('editCharge').value      = charge;
    document.getElementById('editDescription').value = description;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endpush
@endsection

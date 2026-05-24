@extends('layouts.admin')
@section('title', 'Complaint ' . $fieldComplaint->complaint_no)

@section('content')
@php
$fc = $fieldComplaint;
$statusColors = [
    'Pending'    =>'bg-yellow-100 text-yellow-800 border-yellow-200',
    'Assigned'   =>'bg-blue-100 text-blue-800 border-blue-200',
    'In Progress'=>'bg-indigo-100 text-indigo-800 border-indigo-200',
    'Completed'  =>'bg-green-100 text-green-800 border-green-200',
    'Billed'     =>'bg-purple-100 text-purple-800 border-purple-200',
    'Cancelled'  =>'bg-red-100 text-red-800 border-red-200',
];
$priColors=['Low'=>'text-gray-500','Normal'=>'text-blue-600','High'=>'text-orange-600','Urgent'=>'text-red-600 font-bold'];
@endphp

<div class="max-w-4xl mx-auto px-4 py-6 space-y-5">

    {{-- Back + Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.field-complaints.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-bold text-gray-900 font-mono">{{ $fc->complaint_no }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusColors[$fc->status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                    {{ $fc->status }}
                </span>
                <span class="text-sm {{ $priColors[$fc->priority] ?? '' }}">{{ $fc->priority }}</span>
            </div>
            <p class="text-sm text-gray-500">Logged {{ $fc->created_at->diffForHumans() }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.field-complaints.invoice', $fc) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Invoice
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- LEFT: main details --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Customer card --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-indigo-50 border-b border-indigo-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="font-semibold text-indigo-800 text-sm">Customer</span>
                        @if($fc->customer)
                        <span class="text-xs text-indigo-500 font-mono">{{ $fc->customer->customer_id }}</span>
                        @endif
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Name</div>
                            <div class="font-semibold text-gray-800">{{ $fc->customer_name }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Phone</div>
                            <a href="tel:{{ $fc->phone_no }}" class="font-semibold text-indigo-600 hover:underline">{{ $fc->phone_no }}</a>
                        </div>
                        <div class="col-span-2">
                            <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Address</div>
                            <div class="text-gray-700">{{ $fc->address ?: '—' }}</div>
                        </div>
                        @if($fc->location_notes)
                        <div class="col-span-2">
                            <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Location Notes</div>
                            <div class="text-gray-700 italic">{{ $fc->location_notes }}</div>
                        </div>
                        @endif
                    </div>

                    {{-- GPS --}}
                    @if($fc->gps_lat && $fc->gps_lng)
                    <div class="mt-4 bg-emerald-50 border border-emerald-200 rounded-lg p-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                            <div>
                                <div class="text-sm font-semibold text-emerald-800">
                                    {{ $fc->gps_label ?: 'GPS Location' }}
                                </div>
                                <div class="text-xs text-emerald-600 font-mono">{{ $fc->gps_lat }}, {{ $fc->gps_lng }}</div>
                            </div>
                        </div>
                        <a href="{{ $fc->googleMapsUrl() }}" target="_blank"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Open Maps
                        </a>
                    </div>
                    @endif

                    {{-- History from shared DB --}}
                    @if($fc->customer && $fc->customer->fieldComplaints()->count() > 1)
                    <div class="mt-3 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded px-3 py-1.5">
                        ⚠ This customer has {{ $fc->customer->fieldComplaints()->count() - 1 }} other visit(s) on record
                    </div>
                    @endif
                </div>
            </div>

            {{-- Service details --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-blue-50 border-b border-blue-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="font-semibold text-blue-800 text-sm">Service Details</span>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Service Type</div>
                            <div class="font-medium text-gray-800">{{ $fc->service_type_name ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Scheduled</div>
                            <div class="font-medium text-gray-800">{{ $fc->scheduled_date?->format('d M Y') ?? '—' }}</div>
                        </div>
                    </div>
                    @if($fc->description)
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Issue Description</div>
                        <div class="text-gray-700 bg-gray-50 rounded p-2 border border-gray-100">{{ $fc->description }}</div>
                    </div>
                    @endif
                    @if($fc->completion_notes)
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Completion Notes</div>
                        <div class="text-gray-700 bg-green-50 rounded p-2 border border-green-100">{{ $fc->completion_notes }}</div>
                    </div>
                    @endif
                    @if($fc->assigned_to)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Assigned To</div>
                            <div class="font-medium text-gray-800">{{ $fc->assignedEmployee?->employee_name }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Assigned At</div>
                            <div class="text-gray-600">{{ $fc->assigned_at?->format('d M Y, g:i A') ?? '—' }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Bill & items --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-amber-50 border-b border-amber-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M12 7h.01M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                        <span class="font-semibold text-amber-800 text-sm">Billing</span>
                    </div>
                    @if(!in_array($fc->status, ['Billed','Cancelled']))
                    <button onclick="document.getElementById('editBillingModal').classList.remove('hidden')"
                            class="text-xs px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-800 rounded-lg transition font-medium">
                        Edit
                    </button>
                    @endif
                </div>
                <div class="p-5">
                    {{-- Line items --}}
                    @if($fc->items->isNotEmpty())
                    <table class="w-full text-sm mb-3">
                        <thead>
                            <tr class="text-xs text-gray-500 border-b border-gray-100">
                                <th class="text-left pb-2">Description</th>
                                <th class="text-right pb-2">Qty</th>
                                <th class="text-right pb-2">Unit</th>
                                <th class="text-right pb-2">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($fc->items as $item)
                            <tr>
                                <td class="py-1.5 text-gray-700">{{ $item->description }}</td>
                                <td class="text-right text-gray-600">{{ $item->qty }}</td>
                                <td class="text-right text-gray-600">{{ number_format($item->unit_price,2) }}</td>
                                <td class="text-right font-medium">{{ number_format($item->total,2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif

                    <div class="space-y-1.5 text-sm border-t border-gray-100 pt-3">
                        <div class="flex justify-between text-gray-600">
                            <span>Service Charge</span>
                            <span class="font-mono">Rs. {{ number_format($fc->service_charge,2) }}</span>
                        </div>
                        @if($fc->items->isNotEmpty())
                        <div class="flex justify-between text-gray-600">
                            <span>Parts / Labour</span>
                            <span class="font-mono">Rs. {{ number_format($fc->items->sum('total'),2) }}</span>
                        </div>
                        @endif
                        @if($fc->discount > 0)
                        <div class="flex justify-between text-gray-500">
                            <span>Discount</span>
                            <span class="font-mono text-red-500">− Rs. {{ number_format($fc->discount,2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold text-gray-900 border-t border-gray-200 pt-2 mt-2">
                            <span>Grand Total</span>
                            <span class="font-mono">Rs. {{ number_format($fc->grand_total,2) }}</span>
                        </div>
                        <div class="flex justify-between text-green-700">
                            <span>Paid</span>
                            <span class="font-mono">Rs. {{ number_format($fc->paid_amount,2) }}</span>
                        </div>
                        <div class="flex justify-between {{ $fc->balance > 0 ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                            <span>Balance</span>
                            <span class="font-mono">Rs. {{ number_format($fc->balance,2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment logs --}}
            @if($fc->paymentLogs->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                    <span class="font-semibold text-gray-700 text-sm">Payment History</span>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($fc->paymentLogs as $pl)
                        <tr>
                            <td class="px-4 py-2.5 text-gray-500">{{ $pl->paid_at->format('d M Y, g:i A') }}</td>
                            <td class="px-4 py-2.5 text-gray-600">{{ $pl->note ?: 'Payment' }}</td>
                            <td class="px-4 py-2.5 text-right font-mono font-semibold text-green-700">Rs. {{ number_format($pl->amount,2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- RIGHT: actions sidebar --}}
        <div class="space-y-4">

            {{-- Assign --}}
            @if(!in_array($fc->status, ['Completed','Billed','Cancelled']))
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-blue-50 border-b border-blue-100">
                    <span class="font-semibold text-blue-800 text-sm">Assign Field Staff</span>
                </div>
                <form method="POST" action="{{ route('admin.field-complaints.assign', $fc) }}" class="p-4 space-y-3">
                    @csrf @method('PATCH')
                    <select name="assigned_to" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">— Select staff —</option>
                        @foreach($fieldStaff as $emp)
                        <option value="{{ $emp->id }}" {{ $fc->assigned_to == $emp->id ? 'selected' : '' }}>
                            {{ $emp->employee_name }}
                        </option>
                        @endforeach
                    </select>
                    <input type="date" name="scheduled_date" value="{{ $fc->scheduled_date?->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                    <button class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                        Assign
                    </button>
                </form>
            </div>
            @endif

            {{-- Status --}}
            @if($fc->status !== 'Cancelled')
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <span class="font-semibold text-gray-700 text-sm">Update Status</span>
                </div>
                <form method="POST" action="{{ route('admin.field-complaints.status', $fc) }}" class="p-4 space-y-3">
                    @csrf @method('PATCH')
                    <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-400">
                        @foreach(['Pending','Assigned','In Progress','Completed','Billed','Cancelled'] as $s)
                        <option value="{{ $s }}" {{ $fc->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    <textarea name="completion_notes" rows="2" placeholder="Completion / cancellation notes…"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-400 resize-none">{{ $fc->completion_notes }}</textarea>
                    <button class="w-full py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-semibold rounded-lg transition">
                        Update
                    </button>
                </form>
            </div>
            @endif

            {{-- Record payment --}}
            @if($fc->balance > 0)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-green-50 border-b border-green-100">
                    <span class="font-semibold text-green-800 text-sm">Record Payment</span>
                    <div class="text-xs text-green-600">Balance: Rs. {{ number_format($fc->balance,2) }}</div>
                </div>
                <form method="POST" action="{{ route('admin.field-complaints.payment', $fc) }}" class="p-4 space-y-3">
                    @csrf
                    <input type="number" step="0.01" name="amount_paid" placeholder="Amount (Rs.)" required
                           max="{{ $fc->balance }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-green-400">
                    <input type="text" name="note" placeholder="Note (optional)"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400">
                    <button class="w-full py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition">
                        Record
                    </button>
                </form>
            </div>
            @endif

            {{-- Delete --}}
            @if(in_array($fc->status, ['Pending','Cancelled']))
            <form method="POST" action="{{ route('admin.field-complaints.destroy', $fc) }}"
                  onsubmit="return confirm('Delete complaint {{ $fc->complaint_no }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="w-full py-2 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition">
                    Delete Complaint
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- Edit Billing Modal --}}
<div id="editBillingModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h3 class="font-bold text-gray-900">Edit Billing</h3>
            <button onclick="document.getElementById('editBillingModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.field-complaints.update', $fc) }}" class="p-5 space-y-4">
            @csrf @method('PUT')
            {{-- Keep required fields --}}
            <input type="hidden" name="customer_name" value="{{ $fc->customer_name }}">
            <input type="hidden" name="phone_no" value="{{ $fc->phone_no }}">
            <input type="hidden" name="address" value="{{ $fc->address }}">
            <input type="hidden" name="priority" value="{{ $fc->priority }}">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Service Charge (Rs.)</label>
                    <input type="number" step="0.01" name="service_charge" value="{{ $fc->service_charge }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Discount (Rs.)</label>
                    <input type="number" step="0.01" name="discount" value="{{ $fc->discount }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono">
                </div>
            </div>

            {{-- Line items --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-gray-600">Parts / Labour Items</label>
                    <button type="button" id="addItemBtn" class="text-xs px-2 py-1 bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100">+ Add Item</button>
                </div>
                <div id="itemsContainer" class="space-y-2">
                    @foreach($fc->items as $i => $item)
                    <div class="flex gap-2 items-start item-row">
                        <input type="text" name="items[{{ $i }}][description]" value="{{ $item->description }}" placeholder="Description"
                               class="flex-1 border border-gray-200 rounded px-2 py-1.5 text-xs" required>
                        <input type="number" name="items[{{ $i }}][qty]" value="{{ $item->qty }}" placeholder="Qty" min="1"
                               class="w-14 border border-gray-200 rounded px-2 py-1.5 text-xs" required>
                        <input type="number" step="0.01" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}" placeholder="Price"
                               class="w-20 border border-gray-200 rounded px-2 py-1.5 text-xs font-mono" required>
                        <button type="button" class="removeItem text-red-400 hover:text-red-600 mt-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            <button class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">Save Changes</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
let itemIdx = {{ $fc->items->count() }};
document.getElementById('addItemBtn').addEventListener('click', function () {
    const container = document.getElementById('itemsContainer');
    const row = document.createElement('div');
    row.className = 'flex gap-2 items-start item-row';
    row.innerHTML = `
        <input type="text" name="items[${itemIdx}][description]" placeholder="Description" class="flex-1 border border-gray-200 rounded px-2 py-1.5 text-xs" required>
        <input type="number" name="items[${itemIdx}][qty]" placeholder="Qty" min="1" value="1" class="w-14 border border-gray-200 rounded px-2 py-1.5 text-xs" required>
        <input type="number" step="0.01" name="items[${itemIdx}][unit_price]" placeholder="Price" class="w-20 border border-gray-200 rounded px-2 py-1.5 text-xs font-mono" required>
        <button type="button" class="removeItem text-red-400 hover:text-red-600 mt-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
    container.appendChild(row);
    itemIdx++;
});
document.getElementById('itemsContainer').addEventListener('click', function (e) {
    if (e.target.closest('.removeItem')) {
        e.target.closest('.item-row').remove();
    }
});
</script>
@endpush
@endsection

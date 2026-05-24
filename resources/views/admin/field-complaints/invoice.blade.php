@extends('layouts.admin')
@section('title', 'Invoice ' . ($fieldComplaint->invoice_no ?? $fieldComplaint->complaint_no))

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #invoicePrint, #invoicePrint * { visibility: visible; }
    #invoicePrint { position: absolute; inset: 0; }
    .no-print { display: none !important; }
}
</style>
@endpush

@section('content')
@php $fc = $fieldComplaint; @endphp

<div class="max-w-2xl mx-auto px-4 py-6">
    <div class="no-print flex items-center justify-between mb-5">
        <a href="{{ route('admin.field-complaints.show', $fc) }}" class="text-gray-400 hover:text-gray-600 flex items-center gap-1 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
        <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Invoice
        </button>
    </div>

    <div id="invoicePrint" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        {{-- Header --}}
        @php $store = \App\Models\StoreInfo::first(); @endphp
        <div class="bg-indigo-700 text-white px-8 py-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold">{{ $store?->name ?? config('app.name') }}</h1>
                    <p class="text-indigo-200 text-sm mt-1">{{ $store?->address ?? '' }}</p>
                    <p class="text-indigo-200 text-sm">{{ $store?->phone ?? '' }}</p>
                </div>
                <div class="text-right">
                    <div class="text-xs text-indigo-300 uppercase tracking-widest mb-1">Field Service Invoice</div>
                    <div class="text-2xl font-bold font-mono">{{ $fc->invoice_no ?? $fc->complaint_no }}</div>
                    <div class="text-indigo-200 text-sm mt-1">{{ $fc->invoice_date?->format('d M Y') ?? now()->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        <div class="px-8 py-6 space-y-6">
            {{-- Bill to / complaint ref --}}
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Bill To</div>
                    <div class="font-bold text-gray-900 text-lg">{{ $fc->customer_name }}</div>
                    <div class="text-gray-600 text-sm">{{ $fc->phone_no }}</div>
                    @if($fc->address)<div class="text-gray-600 text-sm mt-1">{{ $fc->address }}</div>@endif
                    @if($fc->gps_lat && $fc->gps_lng)
                    <a href="{{ $fc->googleMapsUrl() }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-emerald-600 mt-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                        {{ $fc->gps_label ?? 'Site Location' }}
                    </a>
                    @endif
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Complaint Ref</div>
                    <div class="font-mono font-semibold text-indigo-600">{{ $fc->complaint_no }}</div>
                    <div class="text-sm text-gray-600 mt-1">{{ $fc->service_type_name ?: 'Field Service' }}</div>
                    @if($fc->assignedEmployee)
                    <div class="text-sm text-gray-500 mt-0.5">Tech: {{ $fc->assignedEmployee->employee_name }}</div>
                    @endif
                    @if($fc->scheduled_date)
                    <div class="text-sm text-gray-500 mt-0.5">Visit: {{ $fc->scheduled_date->format('d M Y') }}</div>
                    @endif
                </div>
            </div>

            {{-- Items table --}}
            <div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-y border-gray-200">
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Qty</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Unit</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        {{-- Service charge as first row --}}
                        <tr>
                            <td class="px-3 py-2.5 text-gray-500">1</td>
                            <td class="px-3 py-2.5 font-medium text-gray-800">{{ $fc->service_type_name ?? 'Service Charge' }}</td>
                            <td class="px-3 py-2.5 text-right">1</td>
                            <td class="px-3 py-2.5 text-right font-mono">{{ number_format($fc->service_charge,2) }}</td>
                            <td class="px-3 py-2.5 text-right font-mono">{{ number_format($fc->service_charge,2) }}</td>
                        </tr>
                        @foreach($fc->items as $i => $item)
                        <tr>
                            <td class="px-3 py-2.5 text-gray-500">{{ $i+2 }}</td>
                            <td class="px-3 py-2.5 text-gray-700">{{ $item->description }}</td>
                            <td class="px-3 py-2.5 text-right">{{ $item->qty }}</td>
                            <td class="px-3 py-2.5 text-right font-mono">{{ number_format($item->unit_price,2) }}</td>
                            <td class="px-3 py-2.5 text-right font-mono">{{ number_format($item->total,2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="flex justify-end">
                <div class="w-64 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-mono">Rs. {{ number_format($fc->subtotal,2) }}</span>
                    </div>
                    @if($fc->discount > 0)
                    <div class="flex justify-between text-gray-500">
                        <span>Discount</span>
                        <span class="font-mono text-red-500">− Rs. {{ number_format($fc->discount,2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-gray-900 border-t border-gray-300 pt-2">
                        <span>Total</span>
                        <span class="font-mono">Rs. {{ number_format($fc->grand_total,2) }}</span>
                    </div>
                    <div class="flex justify-between text-green-700">
                        <span>Paid</span>
                        <span class="font-mono">Rs. {{ number_format($fc->paid_amount,2) }}</span>
                    </div>
                    <div class="flex justify-between {{ $fc->balance > 0 ? 'text-red-600 font-bold' : 'text-green-600' }} border-t border-gray-200 pt-2">
                        <span>Balance Due</span>
                        <span class="font-mono">Rs. {{ number_format($fc->balance,2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment history --}}
            @if($fc->paymentLogs->isNotEmpty())
            <div class="border-t border-gray-100 pt-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Payment History</div>
                @foreach($fc->paymentLogs as $pl)
                <div class="flex justify-between text-xs text-gray-500">
                    <span>{{ $pl->paid_at->format('d M Y') }} — {{ $pl->note ?: 'Payment' }}</span>
                    <span class="font-mono">Rs. {{ number_format($pl->amount,2) }}</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Footer --}}
            <div class="border-t border-gray-100 pt-4 text-center text-xs text-gray-400">
                <p>Thank you for choosing {{ $store?->name ?? config('app.name') }}</p>
                @if($store?->email)<p class="mt-1">{{ $store->email }}</p>@endif
            </div>
        </div>
    </div>
</div>
@endsection

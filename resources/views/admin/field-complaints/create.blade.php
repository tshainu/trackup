@extends('layouts.admin')

@section('title', 'New Field Complaint')

@push('styles')
<style>
/* ── Customer search card ───────────────────────────── */
.customer-search-bar          { position:relative; }
.customer-search-bar input    { padding-left:2.8rem; }
.customer-search-bar .srch-icon { position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#6b7280;pointer-events:none; }

#customerFoundBadge   { display:none; }
#customerNewBadge     { display:none; }
#customerHistoryBadge { display:none; }

.gps-preview { background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.5rem;padding:.75rem 1rem; }
.gps-preview a { color:#15803d;font-weight:600; }

.field-group-card { background:#f9fafb;border:1px solid #e5e7eb;border-radius:.75rem;padding:1.25rem 1.25rem .75rem; }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.field-complaints.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">New Field Complaint</h1>
            <p class="text-sm text-gray-500">Log an on-site repair / service request</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.field-complaints.store') }}" id="fcForm">
        @csrf

        {{-- ══════════════════════════════════════════════════════
             SECTION 1 · CUSTOMER
        ══════════════════════════════════════════════════════ --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-5 overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3 bg-indigo-50 border-b border-indigo-100">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h2 class="font-semibold text-indigo-800 text-sm">Customer</h2>
                {{-- badges --}}
                <span id="customerFoundBadge" class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Existing customer
                </span>
                <span id="customerHistoryBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span id="visitCountText">0 visits</span>
                </span>
                <span id="customerNewBadge" class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    New customer
                </span>
            </div>

            <div class="p-5 space-y-4">
                {{-- Phone search --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <div class="customer-search-bar">
                        <svg class="srch-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="tel" id="phoneSearch" name="phone_no" value="{{ old('phone_no') }}"
                               placeholder="Search by phone number…"
                               class="w-full border border-gray-300 rounded-lg py-2.5 pr-3 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                               required autocomplete="off">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Type phone number to auto-search existing customers</p>
                    <input type="hidden" name="customer_db_id" id="customerDbId" value="">
                </div>

                {{-- Auto-filled / editable customer details --}}
                <div id="customerFields" class="{{ old('phone_no') ? '' : 'opacity-60 pointer-events-none' }} transition-opacity">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" id="customerName" value="{{ old('customer_name') }}"
                                   placeholder="Customer name"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                            <input type="email" name="customer_email" id="customerEmail" value="{{ old('customer_email') }}"
                                   placeholder="optional"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Address</label>
                        <textarea name="address" id="customerAddress" rows="2" placeholder="House / apartment, road, city…"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition resize-none">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SECTION 2 · GPS LOCATION
        ══════════════════════════════════════════════════════ --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-5 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 bg-emerald-50 border-b border-emerald-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h2 class="font-semibold text-emerald-800 text-sm">GPS Location</h2>
                </div>
                <button type="button" id="btnGetLocation"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Get My Location
                </button>
            </div>

            <div class="p-5 space-y-4">
                {{-- Paste link --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Paste Location Share Link
                        <span class="font-normal text-gray-400 ml-1">(Google Maps, WhatsApp, any)</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" id="gpsRaw" name="gps_raw" value="{{ old('gps_raw') }}"
                               placeholder="https://maps.google.com/… or 6.9271, 79.8612"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition">
                        <button type="button" id="btnParseLink"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 transition whitespace-nowrap">
                            Parse
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Supports Google Maps links, WhatsApp location, geo: links, or plain "lat, lng" coordinates</p>
                </div>

                {{-- Coords --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Latitude</label>
                        <input type="number" step="0.0000001" name="gps_lat" id="gpsLat" value="{{ old('gps_lat') }}"
                               placeholder="e.g. 6.9271"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Longitude</label>
                        <input type="number" step="0.0000001" name="gps_lng" id="gpsLng" value="{{ old('gps_lng') }}"
                               placeholder="e.g. 79.8612"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition">
                    </div>
                </div>

                {{-- Label --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Location Label</label>
                    <input type="text" name="gps_label" id="gpsLabel" value="{{ old('gps_label') }}"
                           placeholder="e.g. Home, Office, Site A"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition">
                </div>

                {{-- GPS preview --}}
                <div id="gpsPreview" class="gps-preview hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-semibold text-emerald-800" id="gpsPreviewCoords"></span>
                        </div>
                        <a id="gpsPreviewLink" href="#" target="_blank"
                           class="inline-flex items-center gap-1 text-xs text-emerald-700 font-medium hover:underline">
                            Open in Maps
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>

                {{-- From customer DB --}}
                <div id="gpsSavedNote" class="hidden text-xs text-gray-500 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    GPS loaded from customer record — you can update it here
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SECTION 3 · SERVICE DETAILS
        ══════════════════════════════════════════════════════ --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-5 overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3 bg-blue-50 border-b border-blue-100">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h2 class="font-semibold text-blue-800 text-sm">Service Details</h2>
            </div>

            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Service Type</label>
                        <select name="service_type_id" id="serviceTypeSelect"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                            <option value="">— Select service type —</option>
                            @foreach($serviceTypes as $st)
                                <option value="{{ $st->id }}" data-charge="{{ $st->base_charge }}"
                                        {{ old('service_type_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->name }} — Rs.{{ number_format($st->base_charge,2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Priority</label>
                        <select name="priority"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                            @foreach(['Low','Normal','High','Urgent'] as $p)
                                <option value="{{ $p }}" {{ old('priority','Normal') === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fault / Issue Description</label>
                    <textarea name="description" rows="3" placeholder="Describe the fault or issue reported by the customer…"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Scheduled Date</label>
                        <input type="date" name="scheduled_date" value="{{ old('scheduled_date') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Service Charge (Rs.)
                            <span class="font-normal text-gray-400" id="chargeAutoNote"></span>
                        </label>
                        <input type="number" step="0.01" name="rupees" id="serviceCharge" value="{{ old('rupees',0) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Location Notes</label>
                    <input type="text" name="location_notes" value="{{ old('location_notes') }}"
                           placeholder="e.g. Turn left after temple, blue gate"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SECTION 4 · PAYMENT & NOTES
        ══════════════════════════════════════════════════════ --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3 bg-amber-50 border-b border-amber-100">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h2 class="font-semibold text-amber-800 text-sm">Advance Payment & Notes</h2>
            </div>

            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Advance Amount (Rs.)</label>
                        <input type="number" step="0.01" min="0" name="advance_amount" value="{{ old('advance_amount',0) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition">
                        <p class="text-xs text-gray-400 mt-1">Leave 0 if no advance collected</p>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Internal Remark</label>
                    <textarea name="remark" rows="2" placeholder="Any internal notes…"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition resize-none">{{ old('remark') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.field-complaints.index') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Log Complaint
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    /* ── Elements ─────────────────────────────────────────────── */
    const phoneEl      = document.getElementById('phoneSearch');
    const nameEl       = document.getElementById('customerName');
    const emailEl      = document.getElementById('customerEmail');
    const addressEl    = document.getElementById('customerAddress');
    const dbIdEl       = document.getElementById('customerDbId');
    const fieldsWrap   = document.getElementById('customerFields');
    const foundBadge   = document.getElementById('customerFoundBadge');
    const newBadge     = document.getElementById('customerNewBadge');
    const histBadge    = document.getElementById('customerHistoryBadge');
    const visitTxt     = document.getElementById('visitCountText');

    const gpsRawEl     = document.getElementById('gpsRaw');
    const gpsLatEl     = document.getElementById('gpsLat');
    const gpsLngEl     = document.getElementById('gpsLng');
    const gpsLabelEl   = document.getElementById('gpsLabel');
    const gpsPreview   = document.getElementById('gpsPreview');
    const gpsCoordsEl  = document.getElementById('gpsPreviewCoords');
    const gpsLinkEl    = document.getElementById('gpsPreviewLink');
    const gpsSavedNote = document.getElementById('gpsSavedNote');

    const serviceTypeSel = document.getElementById('serviceTypeSelect');
    const serviceChargeEl= document.getElementById('serviceCharge');
    const chargeNoteEl   = document.getElementById('chargeAutoNote');

    /* ── GPS preview updater ──────────────────────────────────── */
    function updateGpsPreview() {
        const lat = parseFloat(gpsLatEl.value);
        const lng = parseFloat(gpsLngEl.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            gpsCoordsEl.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
            gpsLinkEl.href = `https://www.google.com/maps?q=${lat},${lng}`;
            gpsPreview.classList.remove('hidden');
        } else {
            gpsPreview.classList.add('hidden');
        }
    }

    gpsLatEl.addEventListener('input', updateGpsPreview);
    gpsLngEl.addEventListener('input', updateGpsPreview);
    updateGpsPreview(); // on load (old() values)

    /* ── Server-side GPS link parser ──────────────────────────── */
    document.getElementById('btnParseLink').addEventListener('click', function () {
        const raw = gpsRawEl.value.trim();
        if (!raw) return;

        // Try common patterns client-side first (fast)
        let lat = null, lng = null;

        // Plain coords: "6.9271, 79.8612"
        let m = raw.match(/^(-?\d{1,3}\.\d+)[,\s]+(-?\d{1,3}\.\d+)$/);
        if (m) { lat = parseFloat(m[1]); lng = parseFloat(m[2]); }

        // @lat,lng or ?q=lat,lng or ll=lat,lng
        if (!lat) { m = raw.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/); if (m) { lat=parseFloat(m[1]); lng=parseFloat(m[2]); } }
        if (!lat) { m = raw.match(/[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/i); if (m) { lat=parseFloat(m[1]); lng=parseFloat(m[2]); } }
        if (!lat) { m = raw.match(/[?&]ll=(-?\d+\.\d+),(-?\d+\.\d+)/i); if (m) { lat=parseFloat(m[1]); lng=parseFloat(m[2]); } }
        if (!lat) { m = raw.match(/maps\/place\/[^\/]+\/@(-?\d+\.\d+),(-?\d+\.\d+)/i); if (m) { lat=parseFloat(m[1]); lng=parseFloat(m[2]); } }
        if (!lat) { m = raw.match(/geo:(-?\d+\.\d+),(-?\d+\.\d+)/i); if (m) { lat=parseFloat(m[1]); lng=parseFloat(m[2]); } }

        if (lat && lng) {
            gpsLatEl.value = lat;
            gpsLngEl.value = lng;
            updateGpsPreview();
            showAlert('success', `Parsed: ${lat.toFixed(6)}, ${lng.toFixed(6)}`);
        } else {
            showAlert('error', 'Could not extract coordinates from this link. Try pasting plain "lat, lng" instead.');
        }
    });

    /* ── Get My Location ──────────────────────────────────────── */
    document.getElementById('btnGetLocation').addEventListener('click', function () {
        if (!navigator.geolocation) {
            showAlert('error', 'Geolocation not supported in this browser.');
            return;
        }
        this.textContent = 'Getting…';
        this.disabled = true;
        const btn = this;
        navigator.geolocation.getCurrentPosition(
            function (pos) {
                gpsLatEl.value = pos.coords.latitude.toFixed(7);
                gpsLngEl.value = pos.coords.longitude.toFixed(7);
                updateGpsPreview();
                btn.innerHTML = `<svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Got it`;
                btn.disabled = false;
                if (!gpsLabelEl.value) gpsLabelEl.value = 'Field Visit';
            },
            function (err) {
                showAlert('error', 'Location access denied or unavailable.');
                btn.textContent = 'Get My Location';
                btn.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });

    /* ── Customer phone lookup ────────────────────────────────── */
    let searchTimer = null;
    phoneEl.addEventListener('input', function () {
        const val = this.value.trim();
        // Enable customer fields once something typed
        fieldsWrap.classList.toggle('opacity-60', val.length < 3);
        fieldsWrap.classList.toggle('pointer-events-none', val.length < 3);

        if (val.length < 3) {
            resetBadges();
            return;
        }
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => lookupCustomer(val), 400);
    });

    function lookupCustomer(phone) {
        fetch(`/ajax/customer-lookup?phone=${encodeURIComponent(phone)}`)
            .then(r => r.json())
            .then(data => {
                if (data.found) {
                    // Fill fields
                    nameEl.value    = data.name    || '';
                    emailEl.value   = data.email   || '';
                    addressEl.value = data.address || '';
                    dbIdEl.value    = data.customer_id || '';

                    // GPS from customer record
                    if (data.gps_lat && data.gps_lng) {
                        gpsLatEl.value = data.gps_lat;
                        gpsLngEl.value = data.gps_lng;
                        gpsLabelEl.value = data.gps_label || '';
                        updateGpsPreview();
                        gpsSavedNote.classList.remove('hidden');
                    }

                    // Badges
                    foundBadge.style.display = 'inline-flex';
                    newBadge.style.display   = 'none';
                    if (data.visit_count > 0) {
                        histBadge.style.display = 'inline-flex';
                        visitTxt.textContent = data.visit_count + ' previous visit' + (data.visit_count !== 1 ? 's' : '');
                    } else {
                        histBadge.style.display = 'none';
                    }
                } else {
                    // New customer
                    dbIdEl.value = '';
                    foundBadge.style.display = 'none';
                    histBadge.style.display  = 'none';
                    newBadge.style.display   = 'inline-flex';
                }
            })
            .catch(() => {});
    }

    function resetBadges() {
        foundBadge.style.display = 'none';
        newBadge.style.display   = 'none';
        histBadge.style.display  = 'none';
        gpsSavedNote.classList.add('hidden');
    }

    /* ── Service type auto-fill charge ───────────────────────── */
    serviceTypeSel.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const charge = opt.dataset.charge;
        if (charge) {
            serviceChargeEl.value = charge;
            chargeNoteEl.textContent = '(auto from service type)';
        } else {
            chargeNoteEl.textContent = '';
        }
    });

    /* ── Alert helper ─────────────────────────────────────────── */
    function showAlert(type, msg) {
        const div = document.createElement('div');
        div.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium transition ${
            type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
        }`;
        div.textContent = msg;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 3500);
    }
})();
</script>
@endpush

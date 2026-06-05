@extends('layouts.admin')
@section('title', 'New Survey')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.cctv.surveys.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-800">New CCTV Survey</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.cctv.surveys.store') }}" enctype="multipart/form-data" id="surveyForm">
    @csrf

    {{-- ── SURVEY TYPE + MODE ── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Survey Type</label>
                <div class="flex flex-wrap gap-2" id="surveyTypeGroup">
                    @foreach(['New Site','Upgrading','Modification','Service'] as $t)
                    <label class="survey-type-btn cursor-pointer">
                        <input type="radio" name="survey_type" value="{{ $t }}" class="sr-only" {{ old('survey_type','New Site') === $t ? 'checked' : '' }}>
                        <span class="inline-block px-4 py-2 rounded-full border-2 text-sm font-medium transition-all
                            {{ old('survey_type','New Site') === $t ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 text-gray-600 hover:border-blue-400' }}">
                            {{ $t }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Survey Mode</label>
                <div class="flex gap-3" id="surveyModeGroup">
                    @foreach(['Detailed','Simple'] as $m)
                    <label class="survey-mode-btn cursor-pointer">
                        <input type="radio" name="survey_mode" value="{{ $m }}" class="sr-only" {{ old('survey_mode','Detailed') === $m ? 'checked' : '' }}>
                        <span class="inline-block px-5 py-2.5 rounded-lg border-2 text-sm font-semibold transition-all
                            {{ old('survey_mode','Detailed') === $m ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 text-gray-600 hover:border-indigo-400' }}">
                            {{ $m }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── DETAILED SURVEY SECTIONS ── --}}
    <div id="detailedSurvey" class="{{ old('survey_mode','Detailed') === 'Simple' ? 'hidden' : '' }}">

        {{-- Sticky section nav --}}
        <div class="sticky top-0 z-20 bg-white border-b border-gray-200 shadow-sm -mx-4 px-4 py-2 mb-5 overflow-x-auto">
            <div class="flex gap-1 min-w-max text-xs font-medium">
                @foreach([
                    ['s1','Customer'],['s2','Site'],['s3','Purpose'],['s4','Cameras'],
                    ['s5','Network'],['s6','Power'],['s7','Install'],['s8','Materials'],
                    ['s9','Photos'],['s10','Risks'],['s11','Notes']
                ] as [$id,$label])
                <a href="#{{ $id }}" class="px-3 py-1.5 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 whitespace-nowrap transition-colors">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        {{-- ──────────────────────────────────────────────── --}}
        {{-- SECTION 0: Basic Info (always required)         --}}
        {{-- ──────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4" id="s0">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs flex items-center justify-center font-bold">0</span>
                Basic Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Customer search --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Customer Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="customerSearch" autocomplete="off" placeholder="Search or type customer name…"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('customer_name', $lead?->customer_name ?? '') }}">
                        <input type="hidden" name="customer_name" id="customerNameHidden" value="{{ old('customer_name', $lead?->customer_name ?? '') }}">
                        <input type="hidden" name="customer_id" id="customerIdHidden">
                        <input type="hidden" name="lead_id" id="leadIdHidden" value="{{ old('lead_id', $lead?->id ?? '') }}">
                        <div id="customerDropdown" class="hidden absolute z-30 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-52 overflow-y-auto"></div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Mobile</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $lead?->mobile ?? '') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="07X XXX XXXX">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Survey Date</label>
                    <input type="date" name="survey_date" value="{{ old('survey_date', now()->toDateString()) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Technician</label>
                    <div class="relative">
                        <input type="text" id="techSearch" autocomplete="off" placeholder="Search technician…"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('technician_name') }}">
                        <input type="hidden" name="technician_id" id="techIdHidden" value="{{ old('technician_id') }}">
                        <div id="techDropdown" class="hidden absolute z-30 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-52 overflow-y-auto"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ──────────────────── --}}
        {{-- SECTION 1: Customer --}}
        {{-- ──────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4" id="s1">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs flex items-center justify-center font-bold">1</span>
                Customer Details
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Who to contact on-site">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Alt. Mobile</label>
                    <input type="text" name="alt_mobile" value="{{ old('alt_mobile') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Alternative number">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">GPS Location</label>
                    <input type="text" name="gps_location" value="{{ old('gps_location') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Paste Google Maps link or coords">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-2">Customer Type</label>
                    <div class="flex flex-wrap gap-2" id="customerTypeGroup">
                        @foreach(['House','Shop','Office','Factory','School','Hotel','Government','Other'] as $ct)
                        <label class="customer-type-btn cursor-pointer">
                            <input type="radio" name="customer_type" value="{{ $ct }}" class="sr-only" {{ old('customer_type') === $ct ? 'checked' : '' }}>
                            <span class="inline-block px-3 py-1.5 rounded-full border text-xs font-medium transition-all
                                {{ old('customer_type') === $ct ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 text-gray-600 hover:border-blue-400' }}">
                                {{ $ct }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    <div id="customerTypeOtherWrap" class="{{ old('customer_type') === 'Other' ? '' : 'hidden' }} mt-2">
                        <input type="text" name="customer_type_other" value="{{ old('customer_type_other') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Specify type…">
                    </div>
                </div>
            </div>
        </div>

        {{-- ──────────────────── --}}
        {{-- SECTION 2: Site     --}}
        {{-- ──────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4" id="s2">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-green-100 text-green-700 text-xs flex items-center justify-center font-bold">2</span>
                Site Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Building Name / Address</label>
                    <input type="text" name="building_name" value="{{ old('building_name', $lead?->site_address ?? '') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Building Type</label>
                    <input type="text" name="building_type" value="{{ old('building_type') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. 2-storey, Villa…">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Site Size</label>
                    <input type="text" name="site_size" value="{{ old('site_size') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. 40×60 perch, 5000 sqft">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Number of Floors</label>
                    <input type="number" name="num_floors" value="{{ old('num_floors', 1) }}" min="1"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Construction Status</label>
                    <select name="construction_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select —</option>
                        @foreach(['Existing','Under Construction','New Building'] as $cs)
                        <option value="{{ $cs }}" {{ old('construction_status') === $cs ? 'selected' : '' }}>{{ $cs }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3 pt-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="existing_security_system" value="1" {{ old('existing_security_system') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 rounded">
                        <span class="text-sm text-gray-700">Existing Security System?</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ──────────────────────── --}}
        {{-- SECTION 3: Purposes     --}}
        {{-- ──────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4" id="s3">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-yellow-100 text-yellow-700 text-xs flex items-center justify-center font-bold">3</span>
                Purpose / Requirements
            </h2>
            @php
            $purposeOptions = [
                'Theft Prevention','Employee Monitoring','Perimeter Security','Visitor Tracking',
                'Fire/Safety Monitoring','Remote Monitoring','Evidence Recording',
                'Access Control Integration','Child/Elder Safety','General Surveillance',
            ];
            $selectedPurposes = old('purposes', []);
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($purposeOptions as $p)
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" name="purposes[]" value="{{ $p }}" {{ in_array($p, $selectedPurposes) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $p }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- ──────────────────────── --}}
        {{-- SECTION 4: Camera Locs  --}}
        {{-- ──────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4" id="s4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 text-xs flex items-center justify-center font-bold">4</span>
                    Camera Locations
                </h2>
                <button type="button" id="addCamBtn"
                    class="text-xs bg-purple-600 text-white px-3 py-1.5 rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Row
                </button>
            </div>

            {{-- Table header --}}
            <div class="hidden sm:grid grid-cols-12 gap-2 text-xs font-semibold text-gray-500 uppercase mb-2 px-1">
                <div class="col-span-3">Location / Description</div>
                <div class="col-span-2">Indoor/Outdoor</div>
                <div class="col-span-2">Camera Type</div>
                <div class="col-span-1">MP</div>
                <div class="col-span-1 text-center">Night</div>
                <div class="col-span-1 text-center">Audio</div>
                <div class="col-span-2"></div>
            </div>

            <div id="camRows" class="space-y-2">
                {{-- Default first row --}}
                <div class="cam-row grid grid-cols-12 gap-2 items-center bg-gray-50 rounded-lg p-2">
                    <div class="col-span-12 sm:col-span-3">
                        <input type="text" name="cam_location[]" placeholder="e.g. Front Gate"
                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                    </div>
                    <div class="col-span-6 sm:col-span-2">
                        <select name="cam_io[]" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                            <option>Indoor</option><option>Outdoor</option>
                        </select>
                    </div>
                    <div class="col-span-6 sm:col-span-2">
                        <input type="text" name="cam_type[]" placeholder="Dome / Bullet…"
                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                    </div>
                    <div class="col-span-4 sm:col-span-1">
                        <input type="text" name="cam_mp[]" placeholder="2MP"
                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                    </div>
                    <div class="col-span-2 sm:col-span-1 text-center">
                        <input type="checkbox" name="cam_nv[]" value="1" class="w-4 h-4 text-purple-600 rounded" title="Night Vision">
                    </div>
                    <div class="col-span-2 sm:col-span-1 text-center">
                        <input type="checkbox" name="cam_audio[]" value="1" class="w-4 h-4 text-purple-600 rounded" title="Audio">
                    </div>
                    <div class="col-span-4 sm:col-span-2 flex justify-end">
                        <button type="button" class="remove-cam-btn text-red-400 hover:text-red-600 p-1 rounded" title="Remove">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">Each row = one camera position. Night Vision & Audio = checkboxes.</p>
        </div>

        {{-- ──────────────────── --}}
        {{-- SECTION 5: Network  --}}
        {{-- ──────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4" id="s5">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-cyan-100 text-cyan-700 text-xs flex items-center justify-center font-bold">5</span>
                Network / Connectivity
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Internet Status</label>
                    <select name="internet_status" id="internetStatusSel" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select —</option>
                        <option value="Available" {{ old('internet_status') === 'Available' ? 'selected' : '' }}>Available</option>
                        <option value="Not Available" {{ old('internet_status') === 'Not Available' ? 'selected' : '' }}>Not Available</option>
                    </select>
                </div>
                <div id="ispWrap" class="{{ old('internet_status') === 'Available' ? '' : 'hidden' }}">
                    <label class="block text-xs font-medium text-gray-600 mb-1">ISP</label>
                    <select name="isp" id="ispSel" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select —</option>
                        @foreach(['SLT','Dialog','Starlink','Other'] as $isp)
                        <option value="{{ $isp }}" {{ old('isp') === $isp ? 'selected' : '' }}>{{ $isp }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="ispOtherWrap" class="{{ old('isp') === 'Other' ? '' : 'hidden' }}">
                    <label class="block text-xs font-medium text-gray-600 mb-1">ISP Name</label>
                    <input type="text" name="isp_other" value="{{ old('isp_other') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="ISP name…">
                </div>
                <div class="flex flex-col gap-3 sm:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="wifi_coverage" value="1" {{ old('wifi_coverage') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                        <span class="text-sm text-gray-700">WiFi coverage available at site?</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="lan_available" value="1" {{ old('lan_available') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                        <span class="text-sm text-gray-700">LAN / Ethernet available?</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ──────────────────── --}}
        {{-- SECTION 6: Power    --}}
        {{-- ──────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4" id="s6">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-700 text-xs flex items-center justify-center font-bold">6</span>
                Power Supply
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Power Availability</label>
                    <select name="power_availability" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select —</option>
                        @foreach(['Stable','Moderate','Poor'] as $pa)
                        <option value="{{ $pa }}" {{ old('power_availability') === $pa ? 'selected' : '' }}>{{ $pa }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-3 justify-center">
                    @foreach([['ups_required','UPS Required?'],['electrical_work_required','Electrical Work Required?'],['voltage_issues','Voltage Issues Observed?']] as [$fname,$flabel])
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="{{ $fname }}" value="1" {{ old($fname) ? 'checked' : '' }} class="w-4 h-4 text-orange-500 rounded">
                        <span class="text-sm text-gray-700">{{ $flabel }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ────────────────────── --}}
        {{-- SECTION 7: Install    --}}
        {{-- ────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4" id="s7">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-red-100 text-red-700 text-xs flex items-center justify-center font-bold">7</span>
                Installation Assessment
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Cable Route</label>
                    <select name="cable_route" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select —</option>
                        @foreach(['Easy','Medium','Difficult'] as $cr)
                        <option value="{{ $cr }}" {{ old('cable_route') === $cr ? 'selected' : '' }}>{{ $cr }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Ceiling Type</label>
                    <select name="ceiling_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select —</option>
                        @foreach(['Concrete','Gypsum','Metal','Wooden'] as $ct)
                        <option value="{{ $ct }}" {{ old('ceiling_type') === $ct ? 'selected' : '' }}>{{ $ct }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Wall Type</label>
                    <select name="wall_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select —</option>
                        @foreach(['Brick','Concrete','Partition'] as $wt)
                        <option value="{{ $wt }}" {{ old('wall_type') === $wt ? 'selected' : '' }}>{{ $wt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-3 flex flex-wrap gap-4">
                    @foreach([['ladder_required','Ladder Required'],['scaffolding_required','Scaffolding Required']] as [$fname,$flabel])
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="{{ $fname }}" value="1" {{ old($fname) ? 'checked' : '' }} class="w-4 h-4 text-red-500 rounded">
                        <span class="text-sm text-gray-700">{{ $flabel }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Height Risk Level: <span id="heightRiskVal">{{ old('height_risk', 0) }}</span>/10</label>
                    <input type="range" name="height_risk" id="heightRiskRange" min="0" max="10" value="{{ old('height_risk', 0) }}"
                        class="w-full accent-red-500">
                    <div class="flex justify-between text-xs text-gray-400 mt-1"><span>0 (Safe)</span><span>5 (Medium)</span><span>10 (Extreme)</span></div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Special Safety Equipment</label>
                    <input type="text" name="special_safety_equipment" value="{{ old('special_safety_equipment') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. Safety harness…">
                </div>
            </div>
        </div>

        {{-- ────────────────────────── --}}
        {{-- SECTION 8: Materials      --}}
        {{-- ────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4" id="s8">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 text-xs flex items-center justify-center font-bold">8</span>
                Material Estimation
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Total Cameras</label>
                    <input type="number" name="cameras_qty" id="camerasQty" value="{{ old('cameras_qty', 0) }}" min="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">DVR Channels</label>
                    <input type="number" name="dvr_channels" value="{{ old('dvr_channels', 0) }}" min="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">HDD Storage (days)</label>
                    <input type="number" name="hdd_storage_days" value="{{ old('hdd_storage_days', 30) }}" min="1"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Cable (meters)</label>
                    <input type="number" name="cable_meters" value="{{ old('cable_meters', 0) }}" min="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Accessories repeater --}}
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Additional Accessories</p>
                <button type="button" id="addAccBtn"
                    class="text-xs bg-teal-600 text-white px-3 py-1.5 rounded-lg hover:bg-teal-700 transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>
            <div id="accRows" class="space-y-2"></div>
        </div>

        {{-- ─────────────────────── --}}
        {{-- SECTION 9: Site Photos  --}}
        {{-- ─────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4" id="s9">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-pink-100 text-pink-700 text-xs flex items-center justify-center font-bold">9</span>
                Site Photos
            </h2>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors cursor-pointer" id="photoDropZone">
                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-gray-500 mb-1">Tap to add photos or drag & drop</p>
                <p class="text-xs text-gray-400">JPG, PNG, HEIC — multiple allowed</p>
                <input type="file" name="site_photos[]" id="sitePhotosInput" multiple accept="image/*" class="hidden">
            </div>
            <div id="photoPreview" class="grid grid-cols-3 sm:grid-cols-5 gap-2 mt-3"></div>
        </div>

        {{-- ────────────────────── --}}
        {{-- SECTION 10: Risks     --}}
        {{-- ────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4" id="s10">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-red-100 text-red-700 text-xs flex items-center justify-center font-bold">10</span>
                Risk Assessment
            </h2>
            @php
            $riskOptions = [
                'High-rise installation','Confined space','Electrical hazard','Unstable structure',
                'Aggressive animals','Flooding risk','Extreme heat','Poor lighting','Traffic exposure',
                'Customer access restrictions','No signal area','Vandalism risk',
            ];
            $selectedRisks = old('risks', []);
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($riskOptions as $r)
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" name="risks[]" value="{{ $r }}" {{ in_array($r, $selectedRisks) ? 'checked' : '' }}
                        class="w-4 h-4 text-red-500 rounded">
                    <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $r }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- ─────────────────────── --}}
        {{-- SECTION 11: Notes       --}}
        {{-- ─────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6" id="s11">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-600 text-xs flex items-center justify-center font-bold">11</span>
                Special Notes
            </h2>
            <textarea name="special_notes" rows="4"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Any additional observations, customer requests, or instructions…">{{ old('special_notes') }}</textarea>
        </div>

    </div>{{-- end #detailedSurvey --}}

    {{-- ── SIMPLE MODE PLACEHOLDER ── --}}
    <div id="simpleSurvey" class="{{ old('survey_mode','Detailed') === 'Simple' ? '' : 'hidden' }}">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-10 mb-6 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 text-sm font-medium">Simple Survey</p>
            <p class="text-xs text-gray-400 mt-1">Coming soon — use Detailed mode for now.</p>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex justify-end gap-3 pb-8">
        <a href="{{ route('admin.cctv.surveys.index') }}"
            class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">Cancel</a>
        <button type="submit"
            class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors shadow-sm">
            Save Survey
        </button>
    </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
// ─── Leads / Mobile live search ───────────────────────────────────
const leadsData = @json($leads->map(fn($l) => ['id' => $l->id, 'name' => $l->customer_name, 'mobile' => $l->mobile, 'lead_id' => $l->id]));

const custSearch    = document.getElementById('customerSearch');
const custNameHid   = document.getElementById('customerNameHidden');
const custIdHid     = document.getElementById('customerIdHidden');
const leadIdHid     = document.getElementById('leadIdHidden');
const custDrop      = document.getElementById('customerDropdown');

custSearch.addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    custNameHid.value = this.value;
    custIdHid.value   = '';
    leadIdHid.value   = '';
    if (!q) { custDrop.classList.add('hidden'); return; }
    const hits = leadsData.filter(l => l.name.toLowerCase().includes(q) || (l.mobile && l.mobile.includes(q))).slice(0, 8);
    if (!hits.length) { custDrop.classList.add('hidden'); return; }
    custDrop.innerHTML = hits.map(l =>
        `<div class="px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm flex justify-between" data-name="${l.name}" data-lead="${l.lead_id}">
            <span class="font-medium">${l.name}</span>
            <span class="text-gray-400 text-xs">${l.mobile || ''}</span>
        </div>`
    ).join('');
    custDrop.classList.remove('hidden');
    custDrop.querySelectorAll('[data-name]').forEach(el => {
        el.addEventListener('click', function() {
            custSearch.value  = this.dataset.name;
            custNameHid.value = this.dataset.name;
            leadIdHid.value   = this.dataset.lead;
            custDrop.classList.add('hidden');
            // Fill mobile if empty
            const lead = leadsData.find(l => l.lead_id == this.dataset.lead);
            if (lead) {
                const mobileInput = document.querySelector('input[name="mobile"]');
                if (mobileInput && !mobileInput.value) mobileInput.value = lead.mobile || '';
            }
        });
    });
});
document.addEventListener('click', e => { if (!custSearch.contains(e.target) && !custDrop.contains(e.target)) custDrop.classList.add('hidden'); });

// ─── Technician live search ───────────────────────────────────────
const techData = @json($employees->map(fn($e) => ['id' => $e->id, 'name' => $e->employee_name]));
const techSearch = document.getElementById('techSearch');
const techIdHid  = document.getElementById('techIdHidden');
const techDrop   = document.getElementById('techDropdown');

techSearch.addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    techIdHid.value = '';
    if (!q) { techDrop.classList.add('hidden'); return; }
    const hits = techData.filter(e => e.name.toLowerCase().includes(q)).slice(0, 8);
    if (!hits.length) { techDrop.classList.add('hidden'); return; }
    techDrop.innerHTML = hits.map(e =>
        `<div class="px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm" data-id="${e.id}" data-name="${e.name}">${e.name}</div>`
    ).join('');
    techDrop.classList.remove('hidden');
    techDrop.querySelectorAll('[data-id]').forEach(el => {
        el.addEventListener('click', function() {
            techSearch.value = this.dataset.name;
            techIdHid.value  = this.dataset.id;
            techDrop.classList.add('hidden');
        });
    });
});
document.addEventListener('click', e => { if (!techSearch.contains(e.target) && !techDrop.contains(e.target)) techDrop.classList.add('hidden'); });

// ─── Survey Type pill selector ────────────────────────────────────
document.querySelectorAll('.survey-type-btn').forEach(label => {
    label.querySelector('input').addEventListener('change', function() {
        document.querySelectorAll('.survey-type-btn span').forEach(s => {
            s.className = s.className.replace('border-blue-600 bg-blue-600 text-white', 'border-gray-300 text-gray-600 hover:border-blue-400');
        });
        label.querySelector('span').className = label.querySelector('span').className.replace('border-gray-300 text-gray-600 hover:border-blue-400', 'border-blue-600 bg-blue-600 text-white');
    });
});

// ─── Survey Mode toggle ───────────────────────────────────────────
document.querySelectorAll('.survey-mode-btn').forEach(label => {
    label.querySelector('input').addEventListener('change', function() {
        document.querySelectorAll('.survey-mode-btn span').forEach(s => {
            s.className = s.className.replace('border-indigo-600 bg-indigo-600 text-white', 'border-gray-300 text-gray-600 hover:border-indigo-400');
        });
        label.querySelector('span').className = label.querySelector('span').className.replace('border-gray-300 text-gray-600 hover:border-indigo-400', 'border-indigo-600 bg-indigo-600 text-white');
        const mode = this.value;
        document.getElementById('detailedSurvey').classList.toggle('hidden', mode !== 'Detailed');
        document.getElementById('simpleSurvey').classList.toggle('hidden', mode !== 'Simple');
    });
});

// ─── Customer Type pill selector ─────────────────────────────────
document.querySelectorAll('.customer-type-btn').forEach(label => {
    label.querySelector('input').addEventListener('change', function() {
        document.querySelectorAll('.customer-type-btn span').forEach(s => {
            s.className = s.className.replace('border-blue-600 bg-blue-600 text-white', 'border-gray-300 text-gray-600 hover:border-blue-400');
        });
        label.querySelector('span').className = label.querySelector('span').className.replace('border-gray-300 text-gray-600 hover:border-blue-400', 'border-blue-600 bg-blue-600 text-white');
        document.getElementById('customerTypeOtherWrap').classList.toggle('hidden', this.value !== 'Other');
    });
});

// ─── Internet / ISP conditional ───────────────────────────────────
document.getElementById('internetStatusSel').addEventListener('change', function() {
    document.getElementById('ispWrap').classList.toggle('hidden', this.value !== 'Available');
    if (this.value !== 'Available') {
        document.getElementById('ispOtherWrap').classList.add('hidden');
    }
});
document.getElementById('ispSel').addEventListener('change', function() {
    document.getElementById('ispOtherWrap').classList.toggle('hidden', this.value !== 'Other');
});

// ─── Height Risk slider ───────────────────────────────────────────
document.getElementById('heightRiskRange').addEventListener('input', function() {
    document.getElementById('heightRiskVal').textContent = this.value;
});

// ─── Camera rows repeater ─────────────────────────────────────────
function makeCamRow() {
    const div = document.createElement('div');
    div.className = 'cam-row grid grid-cols-12 gap-2 items-center bg-gray-50 rounded-lg p-2';
    div.innerHTML = `
        <div class="col-span-12 sm:col-span-3">
            <input type="text" name="cam_location[]" placeholder="e.g. Back door"
                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
        </div>
        <div class="col-span-6 sm:col-span-2">
            <select name="cam_io[]" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                <option>Indoor</option><option>Outdoor</option>
            </select>
        </div>
        <div class="col-span-6 sm:col-span-2">
            <input type="text" name="cam_type[]" placeholder="Dome / Bullet…"
                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
        </div>
        <div class="col-span-4 sm:col-span-1">
            <input type="text" name="cam_mp[]" placeholder="2MP"
                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
        </div>
        <div class="col-span-2 sm:col-span-1 text-center">
            <input type="checkbox" name="cam_nv[]" value="1" class="w-4 h-4 text-purple-600 rounded" title="Night Vision">
        </div>
        <div class="col-span-2 sm:col-span-1 text-center">
            <input type="checkbox" name="cam_audio[]" value="1" class="w-4 h-4 text-purple-600 rounded" title="Audio">
        </div>
        <div class="col-span-4 sm:col-span-2 flex justify-end">
            <button type="button" class="remove-cam-btn text-red-400 hover:text-red-600 p-1 rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>`;
    return div;
}

document.getElementById('addCamBtn').addEventListener('click', () => {
    const row = makeCamRow();
    document.getElementById('camRows').appendChild(row);
    row.querySelector('input[type="text"]').focus();
    bindRemoveCam(row.querySelector('.remove-cam-btn'));
});

function bindRemoveCam(btn) {
    btn.addEventListener('click', function() {
        const rows = document.querySelectorAll('.cam-row');
        if (rows.length > 1) this.closest('.cam-row').remove();
    });
}
document.querySelectorAll('.remove-cam-btn').forEach(bindRemoveCam);

// ─── Accessories repeater ─────────────────────────────────────────
function makeAccRow() {
    const div = document.createElement('div');
    div.className = 'acc-row flex gap-2 items-center';
    div.innerHTML = `
        <input type="text" name="acc_name[]" placeholder="Item name (e.g. BNC Connector)" required
            class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-teal-400">
        <input type="number" name="acc_qty[]" placeholder="Qty" min="1" value="1"
            class="w-20 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-teal-400">
        <button type="button" class="remove-acc-btn text-red-400 hover:text-red-600 p-1 rounded flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
    div.querySelector('.remove-acc-btn').addEventListener('click', () => div.remove());
    return div;
}
document.getElementById('addAccBtn').addEventListener('click', () => {
    const row = makeAccRow();
    document.getElementById('accRows').appendChild(row);
    row.querySelector('input[type="text"]').focus();
});

// ─── Photo upload preview ─────────────────────────────────────────
const photoInput   = document.getElementById('sitePhotosInput');
const photoPreview = document.getElementById('photoPreview');
const photoDropZone = document.getElementById('photoDropZone');

photoDropZone.addEventListener('click', () => photoInput.click());
photoDropZone.addEventListener('dragover', e => { e.preventDefault(); photoDropZone.classList.add('border-blue-400'); });
photoDropZone.addEventListener('dragleave', () => photoDropZone.classList.remove('border-blue-400'));
photoDropZone.addEventListener('drop', e => {
    e.preventDefault();
    photoDropZone.classList.remove('border-blue-400');
    handleFiles(e.dataTransfer.files);
});
photoInput.addEventListener('change', () => handleFiles(photoInput.files));

function handleFiles(files) {
    photoPreview.innerHTML = '';
    Array.from(files).forEach(file => {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('div');
            img.className = 'relative rounded-lg overflow-hidden aspect-square bg-gray-100';
            img.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            photoPreview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}

// ─── Form submit guard ────────────────────────────────────────────
document.getElementById('surveyForm').addEventListener('submit', function(e) {
    const name = custNameHid.value.trim();
    if (!name) {
        e.preventDefault();
        custSearch.classList.add('ring-2', 'ring-red-400', 'border-red-400');
        custSearch.focus();
        custSearch.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    // Sync customer name hidden
    custNameHid.value = custSearch.value.trim();
});
</script>
@endpush

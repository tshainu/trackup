@extends('layouts.admin')
@section('title', 'Field Complaints')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Field Complaints</h1>
            <p class="text-sm text-gray-500">On-site repair & service requests</p>
        </div>
        <a href="{{ route('admin.field-complaints.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            New Complaint
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-1 mb-4 bg-gray-100 rounded-lg p-1 w-fit">
        @php
        $tabs = [
            'all'       => ['All', $counts['all']],
            'pending'   => ['Pending', $counts['pending']],
            'assigned'  => ['Assigned', $counts['assigned']],
            'inprogress'=> ['In Progress', $counts['inprogress']],
            'completed' => ['Completed', $counts['completed']],
            'billed'    => ['Billed', $counts['billed']],
        ];
        @endphp
        @foreach($tabs as $key => [$label, $count])
        <a href="{{ request()->fullUrlWithQuery(['tab'=>$key,'page'=>1]) }}"
           class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $tab===$key ? 'bg-white shadow text-indigo-700' : 'text-gray-600 hover:text-gray-900' }}">
            {{ $label }}
            <span class="ml-1 text-xs {{ $tab===$key ? 'text-indigo-500' : 'text-gray-400' }}">{{ $count }}</span>
        </a>
        @endforeach
    </div>

    {{-- Search --}}
    <form method="GET" class="mb-4 flex gap-2">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search by complaint#, name, phone, address…"
               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition max-w-md">
        <button class="px-4 py-2 bg-gray-700 text-white text-sm rounded-lg hover:bg-gray-800 transition">Search</button>
        @if($search)<a href="{{ route('admin.field-complaints.index',['tab'=>$tab]) }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>@endif
    </form>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if($complaints->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm">No complaints found</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Complaint#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Assigned</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Priority</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Scheduled</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($complaints as $fc)
                    @php
                    $statusColors = [
                        'Pending'    =>'bg-yellow-100 text-yellow-800',
                        'Assigned'   =>'bg-blue-100 text-blue-800',
                        'In Progress'=>'bg-indigo-100 text-indigo-800',
                        'Completed'  =>'bg-green-100 text-green-800',
                        'Billed'     =>'bg-purple-100 text-purple-800',
                        'Cancelled'  =>'bg-red-100 text-red-800',
                    ];
                    $priColors = ['Low'=>'text-gray-500','Normal'=>'text-blue-600','High'=>'text-orange-600','Urgent'=>'text-red-600 font-bold'];
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.field-complaints.show', $fc) }}"
                               class="font-mono font-semibold text-indigo-600 hover:underline">{{ $fc->complaint_no }}</a>
                            <div class="text-xs text-gray-400">{{ $fc->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $fc->customer_name }}</div>
                            <div class="text-xs text-gray-500">{{ $fc->phone_no }}</div>
                            @if($fc->gps_lat && $fc->gps_lng)
                            <a href="{{ $fc->googleMapsUrl() }}" target="_blank" class="inline-flex items-center gap-0.5 text-xs text-emerald-600 hover:underline mt-0.5">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                GPS
                            </a>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $fc->service_type_name ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-700 text-sm">
                            {{ $fc->assignedEmployee?->employee_name ?? '<span class="text-gray-400">Unassigned</span>' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$fc->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $fc->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm {{ $priColors[$fc->priority] ?? '' }}">{{ $fc->priority }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $fc->scheduled_date?->format('d M') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.field-complaints.show', $fc) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($complaints->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">{{ $complaints->links() }}</div>
        @endif
        @endif
    </div>
</div>
@endsection

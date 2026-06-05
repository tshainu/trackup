<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvSurvey;
use App\Models\CctvLead;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CctvSurveyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');
        $status = $request->get('status');
        $query  = CctvSurvey::with('technician')->latest();
        if ($search) {
            $query->where(fn($q) => $q->where('customer_name', 'like', "%$search%")
                ->orWhere('survey_no', 'like', "%$search%")
                ->orWhere('mobile', 'like', "%$search%"));
        }
        if ($status) {
            $query->where('status', $status);
        }
        $surveys = $query->paginate(20)->withQueryString();

        $allStats  = CctvSurvey::selectRaw('status, count(*) as cnt')->groupBy('status')->pluck('cnt', 'status');
        $stats = [
            'total'     => $allStats->sum(),
            'pending'   => $allStats->get('Pending', 0),
            'completed' => $allStats->get('Completed', 0),
            'quoted'    => $allStats->get('Quoted', 0),
        ];

        return view('admin.cctv.surveys.index', compact('surveys', 'search', 'stats'));
    }

    public function create(Request $request)
    {
        $employees = Employee::where('status', 'active')->orderBy('employee_name')->get();
        $leads     = CctvLead::whereIn('status', ['New Lead', 'Survey Scheduled'])->orderBy('customer_name')->get();
        $leadId    = $request->get('lead_id');
        $lead      = $leadId ? CctvLead::find($leadId) : null;
        return view('admin.cctv.surveys.create', compact('employees', 'leads', 'lead'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:150',
            'mobile'        => 'nullable|string|max:20',
            'survey_date'   => 'nullable|date',
            'technician_id' => 'nullable|exists:employees,id',
            'survey_type'   => 'required|in:New Site,Upgrading,Modification,Service',
            'survey_mode'   => 'required|in:Detailed,Simple',
            'email'         => 'nullable|email|max:150',
            'status'        => 'nullable|in:Scheduled,Completed,Cancelled,Need More Time',
        ]);

        // Handle site photos upload
        $photos = [];
        if ($request->hasFile('site_photos')) {
            foreach ($request->file('site_photos') as $photo) {
                $photos[] = $photo->store('cctv/surveys', 'public');
            }
        }

        // Parse camera_locations repeater rows
        $cameraLocations = [];
        if ($request->has('cam_location')) {
            $locs       = $request->input('cam_location', []);
            $io         = $request->input('cam_io', []);
            $types      = $request->input('cam_type', []);
            $mps        = $request->input('cam_mp', []);
            $nvs        = $request->input('cam_nv', []);
            $audios     = $request->input('cam_audio', []);
            foreach ($locs as $i => $loc) {
                if (trim($loc) === '') continue;
                $cameraLocations[] = [
                    'location'       => $loc,
                    'indoor_outdoor' => $io[$i] ?? 'Indoor',
                    'camera_type'    => $types[$i] ?? '',
                    'mp'             => $mps[$i] ?? '',
                    'night_vision'   => isset($nvs[$i]) ? true : false,
                    'audio'          => isset($audios[$i]) ? true : false,
                ];
            }
        }

        // Parse accessories repeater
        $accessories = [];
        if ($request->has('acc_name')) {
            $accNames = $request->input('acc_name', []);
            $accQtys  = $request->input('acc_qty', []);
            foreach ($accNames as $i => $name) {
                if (trim($name) === '') continue;
                $accessories[] = ['name' => $name, 'qty' => (int)($accQtys[$i] ?? 1)];
            }
        }

        // Purposes & risks are checkboxes → arrays
        $purposes = $request->input('purposes', []);
        $risks    = $request->input('risks', []);

        $isSimple = $request->survey_mode === 'Simple';

        // ── Simple survey data ──────────────────────────────────────────────
        $simpleData = [];
        if ($isSimple) {
            $simpleData = [
                'simple_num_cameras'      => (int)($request->simple_num_cameras ?? 0),
                'simple_dvr_nvr'          => $request->simple_dvr_nvr,
                'simple_dvr_channels'     => $request->simple_dvr_channels ? (int)$request->simple_dvr_channels : null,
                'simple_internet_available' => $request->boolean('simple_internet_available'),
                'simple_isp'              => $request->simple_isp,
                'simple_cabling_ease'     => max(1, min(10, (int)($request->simple_cabling_ease ?? 5))),
                'simple_risk_level'       => max(1, min(10, (int)($request->simple_risk_level ?? 5))),
                'simple_num_technicians'  => (int)($request->simple_num_technicians ?? 1),
                'simple_estimated_days'   => (int)($request->simple_estimated_days ?? 1),
                'simple_gps_location'     => $request->simple_gps_location,
                'simple_remark'           => $request->simple_remark,
            ];
        }

        $survey = CctvSurvey::create(array_merge([
            'survey_no'   => CctvSurvey::nextSurveyNo(),
            'lead_id'     => $request->lead_id,
            'customer_id' => $request->customer_id,
            'status'      => $request->status ?? ($isSimple ? 'Scheduled' : 'Completed'),
            'survey_type' => $request->survey_type,
            'survey_mode' => $request->survey_mode,

            // Basic
            'customer_name' => $request->customer_name,
            'mobile'        => $request->mobile,
            'survey_date'   => $request->survey_date ?? now()->toDateString(),
            'technician_id' => $request->technician_id,
            'special_notes' => $request->special_notes,

            // Section 1
            'contact_person'     => $request->contact_person,
            'alt_mobile'         => $request->alt_mobile,
            'email'              => $request->email,
            'gps_location'       => $request->gps_location,
            'customer_type'      => $request->customer_type,
            'customer_type_other'=> $request->customer_type_other,

            // Section 2
            'building_name'            => $request->building_name,
            'building_type'            => $request->building_type,
            'site_size'                => $request->site_size,
            'existing_security_system' => $request->boolean('existing_security_system'),
            'construction_status'      => $request->construction_status,

            // Section 3
            'purposes' => $purposes ?: null,

            // Section 4
            'camera_locations' => $cameraLocations ?: null,

            // Section 5
            'internet_status' => $request->internet_status,
            'isp'             => $request->isp,
            'isp_other'       => $request->isp_other,
            'wifi_coverage'   => $request->boolean('wifi_coverage'),
            'lan_available'   => $request->boolean('lan_available'),

            // Section 6
            'power_availability'       => $request->power_availability,
            'ups_required'             => $request->boolean('ups_required'),
            'electrical_work_required' => $request->boolean('electrical_work_required'),
            'voltage_issues'           => $request->boolean('voltage_issues'),

            // Section 7
            'cable_route'             => $request->cable_route,
            'ceiling_type'            => $request->ceiling_type,
            'wall_type'               => $request->wall_type,
            'ladder_required'         => $request->boolean('ladder_required'),
            'scaffolding_required'    => $request->boolean('scaffolding_required'),
            'height_risk'             => (int)($request->height_risk ?? 0),
            'special_safety_equipment'=> $request->special_safety_equipment,

            // Section 8
            'cameras_qty'      => (int)($request->cameras_qty ?? 0),
            'dvr_channels'     => (int)($request->dvr_channels ?? 0),
            'hdd_storage_days' => (int)($request->hdd_storage_days ?? 30),
            'cable_meters'     => (int)($request->cable_meters ?? 0),
            'accessories'      => $accessories ?: null,

            // Section 9
            'site_photos'  => $photos ?: null,

            // Section 10
            'risks' => $risks ?: null,

            // Legacy
            'num_floors'        => $request->num_floors ?? 1,
            'indoor_cameras'    => $request->indoor_cameras ?? 0,
            'outdoor_cameras'   => $request->outdoor_cameras ?? 0,
            'internet_available'=> $request->boolean('internet_available'),
            'existing_cctv'     => $request->boolean('existing_cctv'),
        ], $simpleData));

        // Update lead status
        if ($request->lead_id) {
            CctvLead::find($request->lead_id)?->update(['status' => 'Survey Completed']);
        }

        return redirect()->route('admin.cctv.surveys.show', $survey)
            ->with('success', "Survey {$survey->survey_no} created.");
    }

    public function show(CctvSurvey $survey)
    {
        $survey->load('technician', 'lead');
        return view('admin.cctv.surveys.show', compact('survey'));
    }

    public function print(CctvSurvey $survey)
    {
        $survey->load('technician', 'lead');
        $store = \App\Models\StoreInfo::current();
        return view('admin.cctv.surveys.print', compact('survey', 'store'));
    }

    public function edit(CctvSurvey $survey)
    {
        $employees = Employee::where('status', 'active')->orderBy('employee_name')->get();
        $leads     = CctvLead::orderBy('customer_name')->get();
        return view('admin.cctv.surveys.edit', compact('survey', 'employees', 'leads'));
    }

    public function update(Request $request, CctvSurvey $survey)
    {
        $request->validate([
            'customer_name' => 'required|string|max:150',
            'mobile'        => 'nullable|string|max:20',
            'survey_date'   => 'nullable|date',
            'technician_id' => 'nullable|exists:employees,id',
            'survey_type'   => 'required|in:New Site,Upgrading,Modification,Service',
            'survey_mode'   => 'required|in:Detailed,Simple',
            'email'         => 'nullable|email|max:150',
            'status'        => 'nullable|in:Scheduled,Completed,Cancelled,Need More Time',
        ]);

        // Parse camera_locations repeater rows
        $cameraLocations = [];
        if ($request->has('cam_location')) {
            $locs   = $request->input('cam_location', []);
            $io     = $request->input('cam_io', []);
            $types  = $request->input('cam_type', []);
            $mps    = $request->input('cam_mp', []);
            $nvs    = $request->input('cam_nv', []);
            $audios = $request->input('cam_audio', []);
            foreach ($locs as $i => $loc) {
                if (trim($loc) === '') continue;
                $cameraLocations[] = [
                    'location'       => $loc,
                    'indoor_outdoor' => $io[$i] ?? 'Indoor',
                    'camera_type'    => $types[$i] ?? '',
                    'mp'             => $mps[$i] ?? '',
                    'night_vision'   => isset($nvs[$i]),
                    'audio'          => isset($audios[$i]),
                ];
            }
        }

        // Parse accessories
        $accessories = [];
        if ($request->has('acc_name')) {
            foreach ($request->input('acc_name', []) as $i => $name) {
                if (trim($name) === '') continue;
                $accessories[] = ['name' => $name, 'qty' => (int)($request->input('acc_qty', [])[$i] ?? 1)];
            }
        }

        $purposes = $request->input('purposes', []);
        $risks    = $request->input('risks', []);
        $isSimple = $request->survey_mode === 'Simple';

        $simpleData = [];
        if ($isSimple) {
            $simpleData = [
                'simple_num_cameras'        => (int)($request->simple_num_cameras ?? 0),
                'simple_dvr_nvr'            => $request->simple_dvr_nvr,
                'simple_dvr_channels'       => $request->simple_dvr_channels ? (int)$request->simple_dvr_channels : null,
                'simple_internet_available' => $request->boolean('simple_internet_available'),
                'simple_isp'                => $request->simple_isp,
                'simple_cabling_ease'       => max(1, min(10, (int)($request->simple_cabling_ease ?? 5))),
                'simple_risk_level'         => max(1, min(10, (int)($request->simple_risk_level ?? 5))),
                'simple_num_technicians'    => (int)($request->simple_num_technicians ?? 1),
                'simple_estimated_days'     => (int)($request->simple_estimated_days ?? 1),
                'simple_gps_location'       => $request->simple_gps_location,
                'simple_remark'             => $request->simple_remark,
            ];
        }

        $survey->update(array_merge([
            'customer_name'  => $request->customer_name,
            'mobile'         => $request->mobile,
            'survey_date'    => $request->survey_date,
            'technician_id'  => $request->technician_id,
            'survey_type'    => $request->survey_type,
            'survey_mode'    => $request->survey_mode,
            'status'         => $request->status,
            'special_notes'  => $request->special_notes,

            // Section 1
            'contact_person'      => $request->contact_person,
            'alt_mobile'          => $request->alt_mobile,
            'email'               => $request->email,
            'gps_location'        => $request->gps_location,
            'customer_type'       => $request->customer_type,
            'customer_type_other' => $request->customer_type_other,

            // Section 2
            'building_name'            => $request->building_name,
            'building_type'            => $request->building_type,
            'site_size'                => $request->site_size,
            'existing_security_system' => $request->boolean('existing_security_system'),
            'construction_status'      => $request->construction_status,

            // Section 3
            'purposes' => $purposes ?: null,

            // Section 4
            'camera_locations' => $cameraLocations ?: null,

            // Section 5
            'internet_status' => $request->internet_status,
            'isp'             => $request->isp,
            'isp_other'       => $request->isp_other,
            'wifi_coverage'   => $request->boolean('wifi_coverage'),
            'lan_available'   => $request->boolean('lan_available'),

            // Section 6
            'power_availability'       => $request->power_availability,
            'ups_required'             => $request->boolean('ups_required'),
            'electrical_work_required' => $request->boolean('electrical_work_required'),
            'voltage_issues'           => $request->boolean('voltage_issues'),

            // Section 7
            'cable_route'              => $request->cable_route,
            'ceiling_type'             => $request->ceiling_type,
            'wall_type'                => $request->wall_type,
            'ladder_required'          => $request->boolean('ladder_required'),
            'scaffolding_required'     => $request->boolean('scaffolding_required'),
            'height_risk'              => (int)($request->height_risk ?? 0),
            'special_safety_equipment' => $request->special_safety_equipment,

            // Section 8
            'cameras_qty'      => (int)($request->cameras_qty ?? 0),
            'dvr_channels'     => (int)($request->dvr_channels ?? 0),
            'hdd_storage_days' => (int)($request->hdd_storage_days ?? 30),
            'cable_meters'     => (int)($request->cable_meters ?? 0),
            'accessories'      => $accessories ?: null,

            // Risks
            'risks' => $risks ?: null,
        ], $simpleData));

        return redirect()->route('admin.cctv.surveys.show', $survey)->with('success', 'Survey updated.');
    }

    public function destroy(CctvSurvey $survey)
    {
        $no = $survey->survey_no;
        $survey->delete();
        return redirect()->route('admin.cctv.surveys.index')->with('success', "Survey {$no} deleted.");
    }
}

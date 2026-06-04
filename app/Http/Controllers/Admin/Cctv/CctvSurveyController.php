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
        $search   = $request->get('q');
        $query    = CctvSurvey::with('technician')->latest();
        if ($search) {
            $query->where(fn($q) => $q->where('customer_name','like',"%$search%")
                ->orWhere('survey_no','like',"%$search%")
                ->orWhere('mobile','like',"%$search%"));
        }
        $surveys = $query->paginate(20)->withQueryString();
        return view('admin.cctv.surveys.index', compact('surveys','search'));
    }

    public function create(Request $request)
    {
        $employees = Employee::where('status','active')->orderBy('employee_name')->get();
        $leads     = CctvLead::whereIn('status',['New Lead','Survey Scheduled'])->orderBy('customer_name')->get();
        $leadId    = $request->get('lead_id');
        $lead      = $leadId ? CctvLead::find($leadId) : null;
        return view('admin.cctv.surveys.create', compact('employees','leads','lead'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'   => 'required|string|max:150',
            'mobile'          => 'nullable|string|max:20',
            'survey_date'     => 'nullable|date',
            'technician_id'   => 'nullable|exists:employees,id',
            'num_floors'      => 'nullable|integer|min:1',
            'indoor_cameras'  => 'nullable|integer|min:0',
            'outdoor_cameras' => 'nullable|integer|min:0',
            'special_notes'   => 'nullable|string',
        ]);

        $photos = [];
        if ($request->hasFile('site_photos')) {
            foreach ($request->file('site_photos') as $photo) {
                $path = $photo->store('cctv/surveys', 'public');
                $photos[] = $path;
            }
        }

        $survey = CctvSurvey::create([
            'survey_no'        => CctvSurvey::nextSurveyNo(),
            'lead_id'          => $request->lead_id,
            'customer_id'      => $request->customer_id,
            'customer_name'    => $request->customer_name,
            'mobile'           => $request->mobile,
            'survey_date'      => $request->survey_date ?? now()->toDateString(),
            'technician_id'    => $request->technician_id,
            'site_photos'      => $photos ?: null,
            'num_floors'       => $request->num_floors ?? 1,
            'indoor_cameras'   => $request->indoor_cameras ?? 0,
            'outdoor_cameras'  => $request->outdoor_cameras ?? 0,
            'internet_available' => $request->boolean('internet_available'),
            'existing_cctv'    => $request->boolean('existing_cctv'),
            'special_notes'    => $request->special_notes,
            'status'           => 'Completed',
        ]);

        // Update lead status
        if ($request->lead_id) {
            CctvLead::find($request->lead_id)?->update(['status' => 'Survey Completed']);
        }

        return redirect()->route('admin.cctv.surveys.show', $survey)
            ->with('success', "Survey {$survey->survey_no} created.");
    }

    public function show(CctvSurvey $survey)
    {
        $survey->load('technician','lead');
        return view('admin.cctv.surveys.show', compact('survey'));
    }

    public function edit(CctvSurvey $survey)
    {
        $employees = Employee::where('status','active')->orderBy('employee_name')->get();
        return view('admin.cctv.surveys.edit', compact('survey','employees'));
    }

    public function update(Request $request, CctvSurvey $survey)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:150',
            'survey_date'    => 'nullable|date',
            'technician_id'  => 'nullable|exists:employees,id',
        ]);
        $survey->update($request->only(['customer_name','mobile','survey_date','technician_id','num_floors','indoor_cameras','outdoor_cameras','internet_available','existing_cctv','special_notes','status']));
        return redirect()->route('admin.cctv.surveys.show', $survey)->with('success', 'Survey updated.');
    }

    public function destroy(CctvSurvey $survey)
    {
        $no = $survey->survey_no;
        $survey->delete();
        return redirect()->route('admin.cctv.surveys.index')->with('success', "Survey {$no} deleted.");
    }
}

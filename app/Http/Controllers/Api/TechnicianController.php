<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Models\JobCard;
use App\Models\FieldComplaint;

class TechnicianController extends Controller
{
    // ── Auth ──────────────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'password'  => 'required|string',
        ]);

        $employee = Employee::where('user_name', $request->user_name)
                            ->whereIn('status', ['Active','active'])
                            ->first();

        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate/refresh token
        $token = Str::random(60);
        $employee->update(['api_token' => $token]);

        return response()->json([
            'token'    => $token,
            'employee' => [
                'id'            => $employee->id,
                'name'          => $employee->employee_name,
                'user_name'     => $employee->user_name,
                'role'          => $employee->role,
                'type'          => $employee->type,
                'photo'         => $employee->photo,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $emp = $request->attributes->get('auth_employee');
        $emp->update(['api_token' => null]);
        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        $emp = $request->attributes->get('auth_employee');
        return response()->json([
            'id'        => $emp->id,
            'name'      => $emp->employee_name,
            'user_name' => $emp->user_name,
            'role'      => $emp->role,
            'type'      => $emp->type,
            'photo'     => $emp->photo,
        ]);
    }

    // ── Job Cards ─────────────────────────────────────────────────────────────

    public function jobs(Request $request)
    {
        $emp  = $request->attributes->get('auth_employee');
        $jobs = JobCard::where('employee_id', $emp->id)
                       ->whereNotIn('status', ['Completed', 'Cancelled', 'Not Completed'])
                       ->orderByDesc('id')
                       ->get(['id','order_no','customer_name','phone_no','device_name','device_brand',
                              'device_fault','status','priority','date','estimated_delivery',
                              'need_assistant']);

        $all = JobCard::where('employee_id', $emp->id)->get();
        $stats = [
            'pending'     => $all->where('status', 'Pending')->count(),
            'in_progress' => $all->where('status', 'In Progress')->count(),
            'completed'   => $all->whereIn('status', ['Completed'])->count(),
            'total'       => $all->count(),
        ];

        return response()->json(['jobs' => $jobs, 'stats' => $stats]);
    }

    public function allJobs(Request $request)
    {
        $emp    = $request->attributes->get('auth_employee');
        $status = $request->query('status');

        $q = JobCard::where('employee_id', $emp->id)->orderByDesc('id');
        if ($status) $q->where('status', $status);

        return response()->json(['jobs' => $q->get()]);
    }

    public function jobDetail(Request $request, $id)
    {
        $emp = $request->attributes->get('auth_employee');
        $job = JobCard::where('employee_id', $emp->id)->with('invoiceItems')->findOrFail($id);
        return response()->json(['job' => $job]);
    }

    public function acceptJob(Request $request, $id)
    {
        $emp = $request->attributes->get('auth_employee');
        $job = JobCard::where('employee_id', $emp->id)->findOrFail($id);

        if ($job->status !== 'Pending') {
            return response()->json(['error' => 'Job is not in Pending status'], 422);
        }

        $job->update(['status' => 'In Progress']);
        return response()->json(['message' => 'Job accepted', 'job' => $job]);
    }

    public function completeJob(Request $request, $id)
    {
        $emp = $request->attributes->get('auth_employee');
        $job = JobCard::where('employee_id', $emp->id)->findOrFail($id);

        if ($job->status !== 'In Progress') {
            return response()->json(['error' => 'Job is not In Progress'], 422);
        }

        $status = $request->input('status', 'Completed'); // Completed or Not Completed
        $remark = $request->input('remark', '');

        $job->update([
            'status' => in_array($status, ['Completed','Not Completed']) ? $status : 'Completed',
            'remark' => $remark,
        ]);

        return response()->json(['message' => 'Job updated', 'job' => $job]);
    }

    public function requestAssistance(Request $request, $id)
    {
        $emp = $request->attributes->get('auth_employee');
        $job = JobCard::where('employee_id', $emp->id)->findOrFail($id);

        $job->update(['need_assistant' => true]);

        return response()->json(['message' => 'Assistance requested']);
    }

    // ── Field Complaints ──────────────────────────────────────────────────────

    public function allFieldJobs(Request $request)
    {
        $emp  = $request->attributes->get('auth_employee');
        $jobs = FieldComplaint::where('assigned_to', $emp->id)
                              ->whereIn('status', ['Completed', 'Cancelled'])
                              ->orderByDesc('id')
                              ->get(['id','complaint_no','customer_name','phone_no','address',
                                     'service_type_name','description','status','priority',
                                     'scheduled_date','assigned_at','completed_at']);
        return response()->json(['jobs' => $jobs]);
    }

    public function fieldJobs(Request $request)
    {
        $emp  = $request->attributes->get('auth_employee');
        $jobs = FieldComplaint::where('assigned_to', $emp->id)
                              ->whereNotIn('status', ['Completed', 'Cancelled'])
                              ->orderByDesc('id')
                              ->get(['id','complaint_no','customer_name','phone_no','address',
                                     'service_type_name','description','status','priority',
                                     'scheduled_date','assigned_at']);

        $all = FieldComplaint::where('assigned_to', $emp->id)->get();
        $stats = [
            'assigned'    => $all->where('status', 'Assigned')->count(),
            'in_progress' => $all->where('status', 'In Progress')->count(),
            'completed'   => $all->where('status', 'Completed')->count(),
            'total'       => $all->count(),
        ];

        return response()->json(['jobs' => $jobs, 'stats' => $stats]);
    }

    public function fieldJobDetail(Request $request, $id)
    {
        $emp = $request->attributes->get('auth_employee');
        $job = FieldComplaint::where('assigned_to', $emp->id)->with('items')->findOrFail($id);
        return response()->json(['job' => $job]);
    }

    public function acceptFieldJob(Request $request, $id)
    {
        $emp = $request->attributes->get('auth_employee');
        $job = FieldComplaint::where('assigned_to', $emp->id)->findOrFail($id);

        if ($job->status !== 'Assigned') {
            return response()->json(['error' => 'Job is not in Assigned status'], 422);
        }

        $job->update(['status' => 'In Progress']);
        return response()->json(['message' => 'Field job accepted', 'job' => $job]);
    }

    public function completeFieldJob(Request $request, $id)
    {
        $emp = $request->attributes->get('auth_employee');
        $job = FieldComplaint::where('assigned_to', $emp->id)->findOrFail($id);

        if ($job->status !== 'In Progress') {
            return response()->json(['error' => 'Field job is not In Progress'], 422);
        }

        $notes = $request->input('completion_notes', '');
        $job->update([
            'status'           => 'Completed',
            'completed_at'     => now(),
            'completion_notes' => $notes,
        ]);

        return response()->json(['message' => 'Field job completed', 'job' => $job]);
    }

    public function extendFieldJob(Request $request, $id)
    {
        $emp = $request->attributes->get('auth_employee');
        $job = FieldComplaint::where('assigned_to', $emp->id)->findOrFail($id);

        if (!in_array($job->status, ['In Progress', 'Assigned'])) {
            return response()->json(['error' => 'Cannot request extension for this job'], 422);
        }

        $reason = $request->input('reason', '');
        $note   = '[Extension Request] ' . now()->format('d/m/Y H:i') . ': ' . $reason;

        $existing = $job->completion_notes ?? '';
        $job->update([
            'completion_notes' => trim($existing . "\n" . $note),
        ]);

        return response()->json(['message' => 'Extension request recorded', 'job' => $job]);
    }

    public function cantCompleteFieldJob(Request $request, $id)
    {
        $emp = $request->attributes->get('auth_employee');
        $job = FieldComplaint::where('assigned_to', $emp->id)->findOrFail($id);

        if (!in_array($job->status, ['In Progress', 'Assigned'])) {
            return response()->json(['error' => 'Cannot update this job'], 422);
        }

        $reason = $request->input('reason', '');
        $note   = '[Cannot Complete] ' . now()->format('d/m/Y H:i') . ': ' . $reason;

        $existing = $job->completion_notes ?? '';
        $job->update([
            'status'           => 'On Hold',
            'completion_notes' => trim($existing . "\n" . $note),
        ]);

        return response()->json(['message' => 'Job marked as On Hold', 'job' => $job]);
    }
}

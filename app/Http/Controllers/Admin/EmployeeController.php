<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->paginate(20);
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $nextId = 'EMP' . str_pad(Employee::count() + 1, 3, '0', STR_PAD_LEFT);
        return view('admin.employees.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_name'    => 'required|string|max:255',
            'employee_address' => 'nullable|string|max:255',
            'nic'              => 'nullable|string|max:20',
            'phone_no_1'       => 'nullable|string|max:20',
            'phone_no_2'       => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:255',
            'user_name'        => 'required|string|unique:employees,user_name|max:50',
            'role'             => 'required|string|max:50',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        $validated['user_id']     = 'EMP' . str_pad(Employee::count() + 1, 3, '0', STR_PAD_LEFT);
        $validated['password']    = Hash::make($validated['password']);
        $validated['status']      = 'active';
        $validated['registration_no'] = 'REG' . str_pad(Employee::count() + 1, 3, '0', STR_PAD_LEFT);

        Employee::create($validated);

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Employee added successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_name'    => 'required|string|max:255',
            'employee_address' => 'nullable|string|max:255',
            'nic'              => 'nullable|string|max:20',
            'phone_no_1'       => 'nullable|string|max:20',
            'phone_no_2'       => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:255',
            'role'             => 'required|string|max:50',
            'status'           => 'required|in:active,inactive',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }

        $employee->update($validated);

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee removed.');
    }
}

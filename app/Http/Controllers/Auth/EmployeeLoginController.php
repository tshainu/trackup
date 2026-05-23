<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class EmployeeLoginController extends Controller
{
    public function showLogin()
    {
        if (Session::get('employee_logged_in')) return redirect()->route('employee.dashboard');
        return view('auth.employee-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'password'  => 'required|string',
        ]);

        $employee = Employee::where('user_name', $request->user_name)
                            ->where('status', 'active')
                            ->first();

        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return back()->withErrors(['user_name' => 'Invalid credentials.'])->withInput();
        }

        Session::put('employee_logged_in', true);
        Session::put('employee_id', $employee->id);
        Session::put('employee_name', $employee->employee_name);

        return redirect()->route('employee.dashboard');
    }

    public function logout()
    {
        Session::forget(['employee_logged_in', 'employee_id', 'employee_name']);
        return redirect()->route('employee.login');
    }
}

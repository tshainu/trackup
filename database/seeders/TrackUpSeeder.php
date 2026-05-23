<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TrackUpSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        DB::table('admins')->insert([
            'user_name' => 'admin',
            'email' => 'admin@trackup.com',
            'password' => Hash::make('admin123'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Employees
        $employees = [
            ['user_id' => 'EMP1', 'employee_name' => 'Kumaran Raj', 'registration_no' => 'REG001', 'employee_address' => 'Jaffna', 'nic' => '901234567V', 'phone_no_1' => '0771234567', 'phone_no_2' => '0211234567', 'email' => 'kumaran@trackup.com', 'user_name' => 'kumaran', 'role' => 'technician', 'password' => Hash::make('emp123'), 'status' => 'active'],
            ['user_id' => 'EMP2', 'employee_name' => 'Priya Nanthini', 'registration_no' => 'REG002', 'employee_address' => 'Colombo', 'nic' => '912345678V', 'phone_no_1' => '0772345678', 'phone_no_2' => null, 'email' => 'priya@trackup.com', 'user_name' => 'priya', 'role' => 'technician', 'password' => Hash::make('emp123'), 'status' => 'active'],
            ['user_id' => 'EMP3', 'employee_name' => 'Selvam Arasan', 'registration_no' => 'REG003', 'employee_address' => 'Vavuniya', 'nic' => '923456789V', 'phone_no_1' => '0773456789', 'phone_no_2' => null, 'email' => 'selvam@trackup.com', 'user_name' => 'selvam', 'role' => 'helper', 'password' => Hash::make('emp123'), 'status' => 'active'],
        ];
        foreach ($employees as $emp) {
            DB::table('employees')->insert(array_merge($emp, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Device lists
        $devices = ['Television', 'Fan', 'AC', 'Washing Machine', 'Laptop', 'Rice Cooker', 'Refrigerator'];
        $deviceIds = [];
        foreach ($devices as $d) {
            $deviceIds[$d] = DB::table('device_lists')->insertGetId(['device_name' => $d, 'created_at' => now(), 'updated_at' => now()]);
        }

        // Device brands
        $brands = [
            'Television' => ['Sony', 'LG', 'Samsung', 'Toshiba', 'Panasonic'],
            'Fan'        => ['Orient', 'Havells', 'Voltas', 'Crompton'],
            'AC'         => ['LG', 'Samsung', 'Toshiba', 'Sony', 'Carrier', 'Haier'],
            'Washing Machine' => ['LG', 'Samsung', 'Whirlpool', 'IFB'],
            'Laptop'     => ['Acer', 'Dell', 'HP', 'Lenovo', 'Asus'],
            'Rice Cooker' => ['Preethi', 'Panasonic', 'Philips'],
            'Refrigerator' => ['LG', 'Samsung', 'Whirlpool'],
        ];
        foreach ($brands as $device => $list) {
            foreach ($list as $brand) {
                DB::table('device_brands')->insert(['device_list_id' => $deviceIds[$device], 'device_brand' => $brand, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // Device faults
        $faults = [
            'Television'  => ['No power', 'Video not matching audio', 'Screen flickering', 'Remote not working', 'No picture'],
            'Fan'         => ['Reduced speed', 'Not rotating', 'Noisy operation', 'Wobbly fan', 'No power'],
            'AC'          => ['No cooling', 'Refrigerant leak', 'Dirty air filter', 'AC making noises', 'Remote not working'],
            'Washing Machine' => ['Not spinning', 'Water leaking', 'Not draining', 'Vibrating excessively'],
            'Laptop'      => ['Not booting', 'Screen broken', 'Battery not charging', 'Keyboard not working', 'Overheating'],
            'Rice Cooker' => ['Thermal fuse issue', 'Not heating', 'Not switching to warm'],
            'Refrigerator' => ['Not cooling', 'Making noise', 'Water leaking', 'Ice maker broken'],
        ];
        foreach ($faults as $device => $list) {
            foreach ($list as $fault) {
                DB::table('device_faults')->insert(['device_list_id' => $deviceIds[$device], 'device_fault' => $fault, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // Store info
        DB::table('store_info')->insert([
            'store_name' => 'TrackUp Repair Center',
            'registration_no' => 'BRN-2023-001',
            'store_address' => '45 Main Street, Jaffna, Sri Lanka',
            'phone_no1' => '021-1234567',
            'phone_no2' => '077-1234567',
            'owner_name' => 'Rajan Selvakumar',
            'owner_phoneno' => '077-9876543',
            'owner_address' => '12 Temple Road, Jaffna',
            'logo' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sample job cards
        $statuses = ['Pending', 'In Progress', 'Completed', 'Not Completed'];
        $sampleJobs = [
            ['order_no'=>'ORD-2024-001','customer_id'=>'CUS-001','customer_name'=>'Arjun Krishnan','phone_no'=>'0771234501','device_name'=>'Television','device_brand'=>'Sony','serial_no'=>'SN001ABC','device_age'=>'5','device_fault'=>'No power','issue'=>'TV not turning on','date'=>'2024-01-10','rupees'=>1500,'status'=>'Completed','remark'=>'Replaced fuse','need_assistant'=>false,'employee_id'=>1],
            ['order_no'=>'ORD-2024-002','customer_id'=>'CUS-002','customer_name'=>'Meena Lakshmi','phone_no'=>'0772234502','device_name'=>'AC','device_brand'=>'LG','serial_no'=>'SN002DEF','device_age'=>'3','device_fault'=>'No cooling','issue'=>'Room not cooling','date'=>'2024-01-15','rupees'=>3500,'status'=>'In Progress','remark'=>'Gas refill needed','need_assistant'=>false,'employee_id'=>2],
            ['order_no'=>'ORD-2024-003','customer_id'=>'CUS-003','customer_name'=>'Suthan Pillai','phone_no'=>'0773334503','device_name'=>'Fan','device_brand'=>'Orient','serial_no'=>'SN003GHI','device_age'=>'7','device_fault'=>'Reduced speed','issue'=>'Fan running slow','date'=>'2024-02-01','rupees'=>800,'status'=>'Completed','remark'=>'Capacitor replaced','need_assistant'=>false,'employee_id'=>1],
            ['order_no'=>'ORD-2024-004','customer_id'=>'CUS-004','customer_name'=>'Viji Rajan','phone_no'=>'0774434504','device_name'=>'Washing Machine','device_brand'=>'Samsung','serial_no'=>'SN004JKL','device_age'=>'4','device_fault'=>'Not spinning','issue'=>'Clothes not drying','date'=>'2024-02-10','rupees'=>2000,'status'=>'Pending','remark'=>'','need_assistant'=>true,'employee_id'=>null],
            ['order_no'=>'ORD-2024-005','customer_id'=>'CUS-005','customer_name'=>'Karthik Sundaram','phone_no'=>'0775534505','device_name'=>'Laptop','device_brand'=>'Acer','serial_no'=>'SN005MNO','device_age'=>'2','device_fault'=>'Not booting','issue'=>'Laptop freezes on startup','date'=>'2024-03-05','rupees'=>5000,'status'=>'In Progress','remark'=>'Checking motherboard','need_assistant'=>false,'employee_id'=>2],
            ['order_no'=>'ORD-2024-006','customer_id'=>'CUS-006','customer_name'=>'Radha Balakrishnan','phone_no'=>'0776634506','device_name'=>'Rice Cooker','device_brand'=>'Preethi','serial_no'=>'SN006PQR','device_age'=>'9','device_fault'=>'Thermal fuse issue','issue'=>'Not heating at all','date'=>'2024-03-20','rupees'=>600,'status'=>'Completed','remark'=>'Thermal fuse replaced','need_assistant'=>false,'employee_id'=>3],
            ['order_no'=>'ORD-2024-007','customer_id'=>'CUS-007','customer_name'=>'Nirmala Tharmaraj','phone_no'=>'0777734507','device_name'=>'Television','device_brand'=>'Samsung','serial_no'=>'SN007STU','device_age'=>'6','device_fault'=>'Screen flickering','issue'=>'Screen keeps flickering','date'=>'2024-04-02','rupees'=>2500,'status'=>'Not Completed','remark'=>'Parts not available','need_assistant'=>false,'employee_id'=>1],
            ['order_no'=>'ORD-2024-008','customer_id'=>'CUS-008','customer_name'=>'Ganesh Kumar','phone_no'=>'0778834508','device_name'=>'Refrigerator','device_brand'=>'LG','serial_no'=>'SN008VWX','device_age'=>'8','device_fault'=>'Not cooling','issue'=>'Fridge not cold enough','date'=>'2024-04-15','rupees'=>4000,'status'=>'Pending','remark'=>'','need_assistant'=>true,'employee_id'=>null],
            ['order_no'=>'ORD-2024-009','customer_id'=>'CUS-001','customer_name'=>'Arjun Krishnan','phone_no'=>'0771234501','device_name'=>'AC','device_brand'=>'Toshiba','serial_no'=>'SN009YZA','device_age'=>'4','device_fault'=>'AC making noises','issue'=>'Loud noise from unit','date'=>'2024-05-01','rupees'=>1800,'status'=>'Completed','remark'=>'Fan blade cleaned','need_assistant'=>false,'employee_id'=>2],
            ['order_no'=>'ORD-2024-010','customer_id'=>'CUS-009','customer_name'=>'Thilaga Devi','phone_no'=>'0779934509','device_name'=>'Fan','device_brand'=>'Havells','serial_no'=>'SN010BCD','device_age'=>'10','device_fault'=>'Noisy operation','issue'=>'Grinding sound','date'=>'2024-05-10','rupees'=>900,'status'=>'In Progress','remark'=>'Bearing replacement ordered','need_assistant'=>false,'employee_id'=>3],
        ];
        foreach ($sampleJobs as $job) {
            DB::table('job_cards')->insert(array_merge($job, [
                'customer_address' => 'Jaffna, Sri Lanka',
                'customer_email' => 'customer@example.com',
                'customer_nic' => '901234567V',
                'customer_dob' => '1990-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

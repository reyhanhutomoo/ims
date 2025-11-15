<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => 'Register2Controller@register']);
Route::get('/employees/list-employees', 'Register2Controller@index')->name('register.index');
    Route::get('/employees/add-employee', 'Register2Controller@create')->name('register.create');
    Route::post('/employees', 'RegisterController@store')->name('register.store');
Route::get('/home', 'HomeController@index')->name('home');

Route::namespace('Admin')->prefix('admin')->name('admin.')->middleware(['auth','can:admin-access'])->group(function () {
    Route::get('/', 'AdminController@index')->name('index');
    Route::get('/reset-password', 'AdminController@reset_password')->name('reset-password');
    Route::put('/update-password', 'AdminController@update_password')->name('update-password');
    // Route for Manage Account //
    Route::get('/admin/list-account', 'AdminManageController@index')->name('admin.index');
    Route::post('/admin/add-account', 'AdminManageController@store')->name('admin.store');
    Route::put('/admin/{id}', 'AdminManageController@update')->name('admin.update');
    Route::delete('/admin/{id}', 'AdminManageController@destroy')->name('admin.delete');
    // Route for Ip and Location //
    Route::get('/iploc/list-iploc', 'IpLocController@index')->name('iploc.index');
    Route::post('/iploc/add-iploc', 'IpLocController@store')->name('iploc.store');
    Route::put('/iploc/{id}', 'IpLocController@update')->name('iploc.update');
    Route::delete('/iploc/{id}', 'IpLocController@destroy')->name('iploc.delete');
    // Route for Division //
    Route::get('/division/list-division', 'DivisionController@index')->name('division.index');
    Route::post('/division/add-division', 'DivisionController@store')->name('division.store');
    Route::put('/division/{id}', 'DivisionController@update')->name('division.update');
    Route::delete('/division/{id}', 'DivisionController@destroy')->name('division.delete');
    // Route for Campuses //
    Route::get('/campus/list-campus', 'CampusController@index')->name('campus.index');
    Route::post('/campus/add-campus', 'CampusController@store')->name('campus.store');
    Route::put('/campus/{id}', 'CampusController@update')->name('campus.update');
    Route::delete('/campus/{id}', 'CampusController@destroy')->name('campus.delete');
    // Route for Download Attendances //
    Route::get('admin/attendance/download', 'ExportattendancesController@downloadAttendancesPDF')->name('attendance.download');
    // Routes for employees //
    Route::get('/employees/list-employees', 'EmployeeController@index')->name('employees.index');
    Route::get('/employees/add-employee', 'EmployeeController@create')->name('employees.create');
    Route::put('/employees/{id}', 'EmployeeController@update')->name('employees.update');
    // Route::put('/employees/update-employee', 'EmployeeController@update')->name('employees.update');
    Route::post('/employees', 'EmployeeController@store')->name('employees.store');
    Route::put('/employees/attendance/{id}', 'EmployeeController@updateval')->name('employees.attendance.updateval');
    Route::get('/employees/attendance', 'EmployeeController@attendance')->name('employees.attendance.index');
    Route::post('/employees/attendance', 'EmployeeController@attendance')->name('employees.attendance');
    Route::delete('/employees/attendance/{attendance_id}', 'EmployeeController@attendanceDelete')->name('employees.attendance.delete');
    Route::get('/employees/profile/{employee_id}', 'EmployeeController@employeeProfile')->name('employees.profile');
    Route::delete('/employees/{employee_id}', 'EmployeeController@destroy')->name('employees.delete');
    // Routes for leaves //
    Route::get('/leaves/list-leaves', 'LeaveController@index')->name('leaves.index');
    Route::put('/leaves/{leave_id}', 'LeaveController@update')->name('leaves.update');
    // Rote for Weekly Reports //
    Route::get('/employees/weeklyreports', 'WeeklyReportsController@index')->name('employees.weeklyreports');
    Route::put('/emplotees/weeklyreports/{id}', 'WeeklyReportsController@update')->name('employees.weeklyreports.update');
    Route::delete('/employees/weeklyreports/{id}', 'WeeklyReportsController@destroy')->name('employees.weeklyreports.delete');
    Route::get('/employees/weeklyreports/download/{filenName}', 'WeeklyReportsController@download')->name('employees.weeklyreports.download');
    Route::get('/employees/weeklyreports/filter', 'WeeklyReportsController@downloadWeeklyReports')->name('employees.weeklyreports.filter');
});

Route::namespace('Employee')->prefix('employee')->name('employee.')->middleware(['auth','can:employee-access'])->group(function () {
    Route::get('/', 'EmployeeController@index')->name('index');
    Route::get('/profile', 'EmployeeController@profile')->name('profile');
    Route::put('/profile/{id}/updatePhoto', 'EmployeeController@updatePhoto')->name('profile.updatePhoto');
    Route::put('/profile/update-password', 'EmployeeController@update_password')->name('profile.update-password');

    // Route::get('/profile-edit/{employee_id}', 'EmployeeController@profile_edit')->name('profile-edit');
    // // Route::put('/profile/{employee_id}', 'EmployeeController@profile_update')->name('profile-update');
    // // Route::put('/employees/{id}', 'EmployeeController@update')->name('employees.update');
    // Routes for Attendances //
    Route::get('/attendance/list-attendances', 'AttendanceController@index')->name('attendance.index');
    Route::post('/attendance/list-attendances', 'AttendanceController@index')->name('attendance');
    Route::post('/attendance/get-location', 'AttendanceController@location')->name('attendance.get-location');
    Route::get('/attendance/register', 'AttendanceController@create')->name('attendance.create');
    Route::post('/attendance/{employee_id}', 'AttendanceController@store')->name('attendance.store');
    Route::put('/attendance/{attendance_id}', 'AttendanceController@update')->name('attendance.update');
    // Routes for WeeklyReports //
    Route::get('/weeklyreports/list-reports', 'WeeklyReportsController@index')->name('weeklyreports.index');
    Route::post('/weeklyreports/add-reports', 'WeeklyReportsController@store')->name('weeklyreports.store');
    Route::delete('/weeklyreports/{id}', 'WeeklyReportsController@destroy')->name('weeklyreports.delete');
    Route::get('download/{fileName}', 'WeeklyReportsController@download')->name('weeklyreports.download');
    // Routes for Leaves //
    Route::get('/leaves/apply', 'LeaveController@create')->name('leaves.create');
    Route::get('/leaves/list-leaves', 'LeaveController@index')->name('leaves.index');
    Route::post('/leaves/{employee_id}', 'LeaveController@store')->name('leaves.store');
    Route::get('/leaves/edit-leave/{leave_id}', 'LeaveController@edit')->name('leaves.edit');
    Route::put('/leaves/{leave_id}', 'LeaveController@update')->name('leaves.update');
    Route::delete('/leaves/{id}', 'LeaveController@destroy')->name('leaves.delete');
    // Routes for Self //
    Route::get('/self/holidays', 'SelfController@holidays')->name('self.holidays');
    Route::get('/self/salary_slip', 'SelfController@salary_slip')->name('self.salary_slip');
    Route::get('/self/salary_slip_print', 'SelfController@salary_slip_print')->name('self.salary_slip_print');
});
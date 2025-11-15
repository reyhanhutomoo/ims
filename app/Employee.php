<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table = 'employees';
    protected $primaryKey = 'id';
    protected $dates = ['created_at', 'updated_at', 'intern_period'];
    protected $fillable = ['user_id', 'name', 'age', 'campus_id', 'division_id', 'start_date', 'end_date'];
    
    public function user() {
        return $this->belongsTo('App\User');
    }
    public function campus() {
        return $this->belongsTo('App\Campus');
    }
    public function division() {
        return $this->belongsTo('App\Division');
    }
    
    public function attendance() {
        return $this->hasMany('App\Attendance');
    }
    
    public function weeklyreports() {
        return $this->hasMany('App\WeeklyReports');
    }

    public function leave() {
        return $this->hasMany('App\Leave');
    }

    public function expense() {
        return $this->hasMany('App\Expense');
    }
}
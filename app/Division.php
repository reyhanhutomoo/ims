<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;
    protected $table = 'division';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'name',
    ];
    
    public function users() {
        return $this->belongsToMany('App\User');
    }

    public function attendance() {
        return $this->hasMany('App\Attendance');
    }

    public function employees() {
        return $this->hasMany('App\Employee');
    }
}
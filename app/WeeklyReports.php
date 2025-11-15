<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyReports extends Model
{
    use HasFactory;
    protected $table = 'weeklyreports';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'employee_id',
        'tittle',
        'file',
        'description',
        'created_at'
    ];

    public function employee() {
        return $this->belongsTo('App\Employee');
    }
}
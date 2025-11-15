<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillabe = [
        'name',
    ];
    public function users() {
        return $this->belongsToMany('App\User');
    }
}
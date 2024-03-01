<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'companies';

    protected $fillable = [
        'name',
        'logo',
        // 'user_id',
    ];

    // public function user()
    // {
    //     return $this->belongsTo('App\Models\User', 'user_id', 'id');
    // }

    public function team()
    {
        return $this->hasMany('App\Models\Team', 'company_id');
    }

    public function role()
    {
        return $this->hasMany('App\Models\Role', 'company_id');
    }
}

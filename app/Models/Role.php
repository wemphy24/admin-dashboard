<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'roles';
    
    protected $fillable = [
        'name',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function responsibility()
    {
        return $this->hasMany('App\Models\Responsibility', 'role_id');
    }

    public function employee()
    {
        $this->hasMany('App\Models\Employee', 'role_id');
    }
}

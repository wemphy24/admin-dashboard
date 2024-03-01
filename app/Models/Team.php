<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'teams';

    protected $fillable = [
        'name',
        'icon',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function employee()
    {
        $this->hasMany('App\Models\Employee', 'team_id');
    }
}

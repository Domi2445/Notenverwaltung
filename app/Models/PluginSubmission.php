<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PluginSubmission extends Model
{
    protected $fillable = [
        'developer_name',
        'developer_email',
        'package_name',
        'version',
        'description',
        'plan_type',
        'price_cents',
        'currency',
        'status',
    ];
}

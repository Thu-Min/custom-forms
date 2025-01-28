<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormResponse extends Model
{
    protected $fillable = [
        'form_id',
        'form_input_id',
        'response',
    ];

    protected $casts = [
        'response' => 'array',
    ];
}

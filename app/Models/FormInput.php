<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormInput extends Model
{
    protected $fillable = ['form_id', 'label', 'type', 'required', 'options'];
}

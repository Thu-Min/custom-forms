<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = ['name', 'description', 'user_id'];

    public function inputs()
    {
        return $this->hasMany(FormInput::class);
    }

    public function responses()
    {
        return $this->hasMany(FormResponse::class);
    }
}

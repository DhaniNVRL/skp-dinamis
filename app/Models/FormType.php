<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormType extends Model
{
    protected $table = 'form_types';

    protected $fillable = ['name', 'description'];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $table = 'forms';
    
    protected $fillable = [
        'id_groups',
        'name',
        'id_formtype',
    ];

    public function formtype()
    {
        return $this->belongsTo(FormType::class, 'id_formtype', 'id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'form_id'); // pakai form_id sesuai migration
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'id_groups', 'id');
    }
}

<?php

namespace App\Models;

use App\Services\HtmlSanitizer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Description extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'form_id',
        'content',
    ];

    protected function content(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => app(HtmlSanitizer::class)->sanitize($value),
            set: fn (?string $value) => app(HtmlSanitizer::class)->sanitize($value),
        );
    }

    public function group()
    {
        return $this->belongsTo(Group::class,'group_id');
    }

    public function form()
    {
        return $this->belongsTo(
            Form::class,
            'form_id',
            'id'
        );
    }
}

<?php

namespace App\Models;

use App\Models\Traits\HasEncrypt;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasUuids,HasFactory, HasEncrypt;

    protected $fillable = [
        "name", 
        "name_index",
        "logo_url"
    ];

    protected $hidden = [
        'logo_url',
        'name_index',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(...$this->makeEncryptedAttributeCallables('name'));
    } 

    protected static function booted()
    {
        static::saving(function (Organization $organization) {
            $organization->encryptColumnIndex("name", "name_index");
        });
    }
}

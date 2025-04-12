<?php

namespace App\Models;

use App\Enums\RolesEnum;
use App\Models\Traits\HasEncrypt;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasEncrypt;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        "surname",
        'email',
        'password',
        'organization_id',
        "first_name_index",
        "surname_tokens",
        "role",
        "active"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'provider',
        'provider_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            "role" => RolesEnum::class,
            "active" => 'boolean',
        ];
    }

    protected function firstName(): Attribute
    {
        return Attribute::make(...$this->makeEncryptedAttributeCallables('first_name'));
    }

    protected function surname(): Attribute
    {
        return Attribute::make(...$this->makeEncryptedAttributeCallables('surname'));
    }

    protected function email(): Attribute
    {
        return Attribute::make(...$this->makeEncryptedAttributeCallables('email'));
    }

    protected function providerId(): Attribute
    {
        return Attribute::make(...$this->makeEncryptedAttributeCallables('provider_id'));
    } 

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }


}

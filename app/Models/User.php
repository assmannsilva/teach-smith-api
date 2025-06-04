<?php

namespace App\Models;

use App\Enums\ProvidersEnum;
use App\Enums\RolesEnum;
use App\Models\Traits\HasEncrypt;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[ObservedBy([UserObserver::class])]
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
        "email_index",
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
        "email",
        "surname_tokens",
        "first_name_index",
        "email_index",
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
            "provider" => ProvidersEnum::class,
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

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }


}

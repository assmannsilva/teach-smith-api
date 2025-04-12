<?php

namespace App\Models;

use App\Models\Traits\HasEncrypt;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{

    use HasUuids, HasFactory, HasEncrypt;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cpf',
        'bio',
        'degree',
        'hire_date',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'user_id',
    ];

    public function casts(): array
    {
        return [
            'cpf' => 'string',
            'bio' => 'string',
            'degree' => 'string',
            'hire_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function cpf(): Attribute
    {
        return Attribute::make(...$this->makeEncryptedAttributeCallables('cpf'));
    }
}

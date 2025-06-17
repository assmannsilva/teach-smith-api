<?php

namespace App\Models;

use App\Enums\GradeLevelEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    
    use HasUuids, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'grade_level',
        "section",
        "year",
    ];

    public function casts(): array
    {
        return [
            'grade_level' => GradeLevelEnum::class,
            'section' => 'string',
            'year' => 'integer',
        ];
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'classrom_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
}

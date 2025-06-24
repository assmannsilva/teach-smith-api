<?php

namespace App\Models;

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
        'grade',
        "education_stage",
        "section",
        "year",
    ];

    public function casts(): array
    {
        return [
            'grade' => "string",
            'section' => 'string',
            'education_stage' => 'string',
            'year' => 'integer',
        ];
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'classrom_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
}

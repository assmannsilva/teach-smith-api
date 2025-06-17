<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasUuids, HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name'];


    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classrom_id');
    }
}

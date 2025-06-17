<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Interfaces\StudentRepositoryInterface;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    protected $modelClass = Student::class;
}
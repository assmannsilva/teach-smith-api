<?php

namespace App\Repositories;

use App\Models\Subject;
use App\Repositories\Interfaces\SubjectRepositoryInterface;

class SubjectRepository extends BaseRepository implements SubjectRepositoryInterface
{
    protected $modelClass = Subject::class;
}
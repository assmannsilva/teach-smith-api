<?php
namespace App\Repositories;

use App\Models\Classroom;
use App\Repositories\Interfaces\ClassroomRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ClassroomRepository extends BaseRepository implements ClassroomRepositoryInterface {

    protected $modelClass = Classroom::class;

    /**
     * Find a classroom by grade and section in the current academic year.
     * @param string $grade The grade of the classroom.
     * @param string $section The section of the classroom.
     * @param string $organization_id The organization ID of the classroom.
     * @return ?Classroom The classroom model instance.
     */
    public function findByGradeAndSectionInCurrentYear(string $grade, string $section, string $organization_id): ?Classroom
    {
        $currentYear = Carbon::now()->year;
        return $this->newQuery()
            ->where("organization_id",$organization_id)
            ->where('grade', $grade)
            ->where('section', $section)
            ->where('year', $currentYear)
            ->first();
    }

    /**
     * Get existing classrooms based on the provided grade and section combinations for the current academic year.
     * @param array $grades_sections {grades, section}. An array of grade and section combinations.
     * @return array An array of existing grade/sections.
     */
    public function getExistingGradesSectionsInCurrentYear(array $grades_sections): array
    {
        if(count($grades_sections) == 0) return [];

        $bindings = [];
        $placeholders = \collect($grades_sections)->map(function ($pair) use (&$bindings) {
            $bindings[] = $pair['grade'];
            $bindings[] = $pair['section'];
            return '(?, ?)';
        })->implode(', ');

        $currentYear = Carbon::now()->year;
        return $this->newQuery()
            ->where("organization_id",Auth::user()->organization_id)
            ->where('year', $currentYear)
            ->whereRaw(
                "(grade,section) IN ($placeholders)",
                $bindings
            )
            ->get(['grade', 'section'])
            ->toArray();
    }
}
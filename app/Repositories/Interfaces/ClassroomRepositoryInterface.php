<?php

namespace App\Repositories\Interfaces;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

interface ClassroomRepositoryInterface { 

    /**
     * Find a classroom by grade and section in the current academic year.
     * @param string $grade The grade of the classroom.
     * @param string $section The section of the classroom.
     * @param string $organization_id The organization ID of the classroom.
     * @return ?Classroom The classroom model instance.
     */
    public function findByGradeAndSectionInCurrentYear(String $grade, String $section, string $organization_id) : ?Classroom;

    /**
     * Get existing classrooms based on the provided grade and section combinations for the current academic year.
     * @param array $grades_sections An array of grade and section combinations.
     * @return array An array of existing grade/sections.
     */
    public function getExistingGradesSectionsInCurrentYear(array $grades_sections): array;
}
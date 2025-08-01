<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Models\Teacher;
use App\Repositories\Interfaces\TeacherRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TeacherService {

    private const SELECT_LIMIT = 6;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected TeacherRepositoryInterface $teacherRepository
    ) { }

    /**
     * Create a teacher and its user from an invitation data array.
     *
     * @param array{
     *   email: string,
     *   first_name: string,
     *   surname: string,
     *   organization_id: int,
     *   cpf: string,
     *   degree: string,
     *   hire_date: string
     * } $insert_data
     * @return Teacher
     */
    public function createFromInvitation($insert_data) : Teacher
    {
        $teacher_transaction = DB::transaction(function () use ($insert_data) {
            $user = $this->userRepository->create([
                'email' => $insert_data['email'],
                'first_name' => $insert_data['first_name'],
                'surname' => $insert_data['surname'],
                'organization_id' => $insert_data['organization_id'],
                'role' => RolesEnum::TEACHER,
                'active' => false,
            ]);
    
            return $this->teacherRepository->create([
                'user_id' => $user->id,
                'cpf' => $insert_data['cpf'],
                'degree' => $insert_data['degree'],
                'hire_date' => $insert_data['hire_date'],
            ]);
        });

        return $teacher_transaction;
    }

    public function searchTeachersByName(string $name)
    {
        return $this->userRepository->searchByNameAndRole(
            $name,
            RolesEnum::TEACHER,
            self::SELECT_LIMIT
        );

    }
}
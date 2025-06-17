<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Models\Student;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StudentService {

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected StudentRepositoryInterface $studentRepository
    ) { }


    /**
     * Create a student and its user from an invitation data array.
     *
     * @param array{
     *   email: string,
     *   first_name: string,
     *   surname: string,
     *   organization_id: int,
     *   registration_code: string,
     *   grade_level: string,
     *   admission_date: string
     * } $insert_data
     * @return Student
     */
    public function createFromInvitation($insert_data) : Student
    {
        $student_transaction = DB::transaction(function () use ($insert_data) {
            $user = $this->userRepository->create([
                'email' => $insert_data['email'],
                'first_name' => $insert_data['first_name'],
                'surname' => $insert_data['surname'],
                'organization_id' => $insert_data['organization_id'],
                'role' => RolesEnum::STUDENT,
                'active' => false,
            ]);
    
            return $this->studentRepository->create([
                'user_id' => $user->id,
                'registration_code' => $insert_data['registration_code'],
                'grade_level' => $insert_data['grade_level'],
                'admission_date' => $insert_data['admission_date'],
            ]);
        });

        return $student_transaction;
    }
}
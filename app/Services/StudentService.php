<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Models\Student;
use App\Repositories\Interfaces\ClassroomRepositoryInterface;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\User\InviteUserService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class StudentService {

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected StudentRepositoryInterface $studentRepository,
        protected ClassroomRepositoryInterface $classroomRepository,
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
     *   grade: string,
     *   section: string,
     *   admission_date: string
     * } $insert_data
     * @return Student
     */
    public function createFromInvitation($insert_data) : Student
    {
        $classroom = $this->classroomRepository->findByGradeAndSectionInCurrentYear(
            $insert_data['grade'],
            $insert_data['section'],
            $insert_data['organization_id']
        );

        $student_transaction = DB::transaction(function () use ($insert_data, $classroom) {
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
                'admission_date' => $insert_data['admission_date'],
                "classroom_id" => $classroom->id
            ]);
        });

        return $student_transaction;
    }
}
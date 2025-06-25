<?php
namespace App\Lib\DispatchUserInvites;

use App\Http\Requests\Users\Invites\InviteStudentsRequest;
use App\Imports\UsersImport;
use App\Models\Student;
use App\Repositories\Interfaces\ClassroomRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

class DispatchStudentInvites extends DispatchUserInvitesTemplate
{
    protected String $userModelRelatedClass = Student::class;
    protected String $formRequestValidationClass = InviteStudentsRequest::class;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UsersImport $usersImport,
        protected ClassroomRepositoryInterface $classroomRepository
    ) {
        parent::__construct($userRepository, $usersImport);
    }

    private function validateClassrooms(array $users_raw_data) : array
    {
        $grades_and_sections =  collect($users_raw_data)
        ->map(fn($user) => ['grade' => $user['grade'], 'section' => $user['section']])
        ->unique(fn($item) => $item['grade'] . '-' . $item['section'])
        ->values()
        ->toArray();

        $existing_classrooms = $this->classroomRepository->getExistingGradesSectionsInCurrentYear($grades_and_sections);
        $must_create_users = \array_filter($users_raw_data, function ($user) use ($existing_classrooms) {
            return \in_array(
                ["grade" => $user['grade'], "section" => $user['section']],
                $existing_classrooms
            );
        });

        return [
            'must_create_users' => $must_create_users,
            'total_non_existing_classrooms' => \count($grades_and_sections) - \count($existing_classrooms),
        ];
    }

    protected function applyValidations(array $users_raw_data): array
    {
        $filter_response = $this->filterDuplicatedEmails($users_raw_data);
        $filter_classrooms_response = $this->validateClassrooms($filter_response["must_create_users"]);
        unset($filter_response["must_create_users"]);
        
        return [
            ...$filter_response,
            ...$filter_classrooms_response
        ];
    }
}

?>
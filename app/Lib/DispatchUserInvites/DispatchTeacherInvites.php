<?php
namespace App\Lib\DispatchUserInvites;

use App\Http\Requests\Users\Invites\InviteTeachersRequest;
use App\Imports\UsersImport;
use App\Models\Teacher;
use App\Repositories\Interfaces\UserRepositoryInterface;

class DispatchTeacherInvites extends DispatchUserInvitesTemplate
{
    protected String $userModelRelatedClass = Teacher::class;
    protected String $formRequestValidationClass = InviteTeachersRequest::class;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UsersImport $usersImport,
    ) {
        parent::__construct($userRepository, $usersImport);
    }

    protected function applyValidations(array $users_raw_data): array
    {
        return $this->filterDuplicatedEmails($users_raw_data); 
    }
}

?>
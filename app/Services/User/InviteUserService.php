<?php

namespace App\Services\User;

use App\Jobs\CreateUserRegistration;
use App\Mail\InviteRegistration;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\TeacherService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class InviteUserService {
    
    public function __construct(
        protected TeacherService $teacher_service,
        protected UserRepositoryInterface $user_repository_interface
 
    ) { }

    /**
     * Dispatches the creation of users and returns the already registered emails (not created again).
     *
     * @param array $users
     * @param string $model_class_name
     * @return array {dispatched_count, already_registered_count, duplicated_emails}
     */
    public function dispatchInvites(array $users, string $model_class_name) : array
    {
        $emails = array_column($users, 'email');
        $existent_emails = $this->user_repository_interface->getExistingEmails($emails);

        $must_create_users = \array_filter(
            $users,
            fn($user) => !in_array($user['email'],$existent_emails)
        );
        
        foreach($must_create_users as $user) CreateUserRegistration::dispatch($user,$model_class_name);

        return [
            "dispatched_count" => count($must_create_users),
            "duplicated_emails_count" => count($existent_emails),
            "duplicated_emails" => $existent_emails,
        ];
    }

    public function invite(User $user) : void
    {
        $complete_registration_url = URL::signedRoute('complete-registration', ['id' => $user->id]);
        Mail::to($user->email)->queue(new InviteRegistration($user,$complete_registration_url));
    }
}
<?php

namespace App\Services\User;

use App\Imports\Interfaces\BaseImportInterface;
use App\Jobs\CreateUserRegistration;
use App\Mail\InviteRegistration;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class InviteUserService {
    
    public function __construct(
        protected UserRepositoryInterface $user_repository_interface
    ) { }
    
    /**
     * Imports users from a file and dispatches invites for valid data.
     * @param BaseImportInterface $import
     * @param UploadedFile $import_file
     * @param string $model_class_name
     * @return array {dispatched_count, duplicated_emails, errors}
     */
    public function importUsers(
        BaseImportInterface $import,
        UploadedFile $import_file,
        string $model_class_name
    ) : array
    {
        $import->import($import_file);
        $errors     = $import->getDataErrors();
        $valid_data = $import->getValidData();
        $response   = $this->dispatchInvites($valid_data,$model_class_name);

        return [
            'dispatched_count' => $response['dispatched_count'],
            'duplicated_emails' => $response['duplicated_emails'],
            'errors' => $errors,
        ];
    }

    /**
     * Dispatches the creation of users and returns the already registered emails (not created again).
     *
     * @param array $users
     * @param string $model_class_name
     * @return array {dispatched_count, duplicated_emails}
     */
    public function dispatchInvites(array $users_raw_data, string $model_class_name) : array
    {
        $emails = array_column($users_raw_data, 'email');
        $existent_emails = $this->user_repository_interface->getExistingEmails($emails);

        $must_create_users = \array_filter(
            $users_raw_data,
            fn($user) => !in_array($user['email'],$existent_emails)
        );
        
        foreach($must_create_users as $user) {
            CreateUserRegistration::dispatch($user,$model_class_name, Auth::user()->organization_id);
        }

        return [
            "dispatched_count" => count($must_create_users),
            "duplicated_emails" => $existent_emails,
        ];
    }

    public function invite(User $user) : void
    {
        //$complete_registration_url = URL::signedRoute('complete-registration', ['id' => $user->id]);
        $complete_registration_url = "https://google.com";
        Mail::to($user->email)->queue(new InviteRegistration($user,$complete_registration_url));
    }
}
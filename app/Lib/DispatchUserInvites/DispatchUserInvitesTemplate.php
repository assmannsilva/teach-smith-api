<?php
namespace App\Lib\DispatchUserInvites;

use App\Imports\UsersImport;
use App\Jobs\CreateUserRegistration;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

abstract class DispatchUserInvitesTemplate
{
    protected String $userModelRelatedClass;
    protected String $formRequestValidationClass;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UsersImport $usersImport
    ) { }

    /**
     * Imports users from a file and returns valid and invalid data.
     * @param UploadedFile $import_file
     * @return array { $valid_data, $total_data_errors}
     */
    protected function importUsersFile(UploadedFile $import_file) : array
    {
        $this->usersImport->setFormRequestValidation($this->formRequestValidationClass);
        $this->usersImport->import($import_file);
        return [
            "valid_data" => $this->usersImport->getValidData(),
            "total_data_errors" => $this->usersImport->getTotalErrorsCount()
        ];
    }

    /**
     * Filters out users whose emails already exist in the database.
     *
     * @param array $users_raw_data
     * @return array {$must_create_users, $total_duplicated}
     */
    protected function filterDuplicatedEmails(array $users_raw_data) : array
    {
        $emails = array_column($users_raw_data, 'email');
        $existent_emails = $this->userRepository->getExistingEmails($emails);

        $must_create_users = \array_filter(
            $users_raw_data,
            fn($user) => !in_array($user['email'],$existent_emails)
        );

        return [
            "must_create_users" => $must_create_users,
            "total_duplicated" => \count($existent_emails)
        ];
    }

    /**
     * Dispatches the creation of users.
     *
     * @param array $users
     * @return int $dispatched_count
     */
    protected function dispatchInvites(array $must_create_users) : int
    {
        foreach($must_create_users as $user_data) {
            CreateUserRegistration::dispatch(
                $user_data,
                $this->userModelRelatedClass, 
                Auth::user()->organization_id
            );
        }

        return count($must_create_users);
    }

    /**
     * Applies user-type specific validations.
     *
     * @param array $users_raw_data
     * @return array
     */
    abstract protected function applyValidations(array $users_raw_data): array;

    /**
     * Handles import and dispatch flow.
     * @param UploadedFile $import_file
     * @return array 
     */
    public function handleImportDispatch(UploadedFile $import_file)
    {
        $import_response_data   = $this->importUsersFile($import_file);
        $validation             = $this->applyValidations($import_response_data['valid_data']);
        $dispatched_count       = $this->dispatchInvites($validation['must_create_users']);
        unset($validation['must_create_users']);
        unset($import_response_data["valid_data"]);
        
        return [
            'dispatched' => $dispatched_count,
            'errors' => [
                ...$import_response_data,
                ...$validation
            ]
        ];
    }

     /**
     * Handles a single user invite dispatch.
     * @param array $user_data
     * @return array
     */
    public function handleSingleUserInvite(array $user_data) : array
    {
        $validation         = $this->applyValidations([$user_data]);
        $dispatched_count   = $this->dispatchInvites($validation['must_create_users']);
        unset($validation['must_create_users']);

        return [
            'dispatched' => $dispatched_count,
            'errors' => [...$validation ] 
        ];
    }

}
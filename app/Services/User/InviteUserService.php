<?php

namespace App\Services\User;

use App\Lib\DispatchUserInvites\DispatchUserInvitesTemplate;
use App\Mail\InviteRegistration;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

class InviteUserService {
    
    /**
     * Imports users from a file and dispatches invites for valid data.
     * @param UploadedFile $import_file
     * @param DispatchUserInvitesTemplate $dispatcher
     */
    public function dispatchMultipleInvites(
        UploadedFile $import_file,
        DispatchUserInvitesTemplate $dispatcher
    ) : array
    {
        return $dispatcher->handleImportDispatch($import_file);
    }

    /**
     * Dispatches a single user invite based on request data.
     * @param UploadedFile $import_file
     * @param DispatchUserInvitesTemplate $dispatcher
     */
    public function dispatchSingleInvite(
        array $request_data,
        DispatchUserInvitesTemplate $dispatcher
    ) : array
    {
        return $dispatcher->handleSingleUserInvite($request_data);
    }

    /**
     * Sends an invite email to a user.
     *
     * @param User $user
     * @return void
     */
    public function invite(User $user) : void
    {
        //$complete_registration_url = URL::signedRoute('complete-registration', ['id' => $user->id]);
        $complete_registration_url = "https://google.com";
        Mail::to($user->email)->queue(new InviteRegistration($user,$complete_registration_url));
    }
}
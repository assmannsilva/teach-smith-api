<?php

namespace App\Http\Controllers\Users\Invites;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Invites\InviteTeachersRequest;
use App\Models\Teacher;
use App\Services\User\CsvErrorReportService;
use App\Services\User\InviteUserService;
use Throwable;
use Illuminate\Support\Facades\Log;

class InviteTeachersController extends Controller
{
    public function __invoke(
        InviteTeachersRequest $request,
        InviteUserService $invite_user_service,
        CsvErrorReportService $csv_error_report_service     
    ) {
        try {
            $result = $invite_user_service->dispatchInvites($request->input("teachers"),Teacher::class);

            $csv_error_path = null;
            if(!empty($result["duplicated_emails"])) {
                $csv_error_path = $csv_error_report_service->handle(["email"],$result["duplicated_emails"]);
            }

            return response()->json([
                "dispatched_count" => $result["dispatched_count"],
                "duplicated_count" => $result["duplicated_emails_count"],
                "messsage" => $result["dispatched_count"] > 0 ? "Invites dispatched" : "Already registered emails",
                "duplicated_emails_report" => $csv_error_path,
            ], $result["dispatched_count"] > 0 ? 200 : 400);
            
        } catch (Throwable $th) {
            
            Log::error("Error dispatching invites", [
                "error" => $th->getMessage(),
                "stack" => $th->getTraceAsString()
            ]);
            
            return \response([
                "message" => "an unexpected error occurred"
            ],500);
        }
    }
}

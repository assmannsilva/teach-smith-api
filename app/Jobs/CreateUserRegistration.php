<?php

namespace App\Jobs;

use App\Models\Teacher;
use App\Services\Invites\InviteUserService;
use App\Services\TeacherService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateUserRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $modelServiceClasses = [
        Teacher::class => TeacherService::class,
    ];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $userData,
        public string $publicModelClassName
    ) { }

    /**
     * Execute the job.
     */
    public function handle(InviteUserService $inviteUserService): void
    {  
        $serviceClass = $this->modelServiceClasses[$this->publicModelClassName]
        ?? throw new \InvalidArgumentException("No service found for model [{$this->publicModelClassName}]");

        $model_service = app($serviceClass);

        $model = $model_service->createFromInvitation($this->userData);
        $inviteUserService->invite($model->user);
    }
}

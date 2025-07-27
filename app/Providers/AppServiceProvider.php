<?php

namespace App\Providers;

use App\Repositories\ClassroomRepository;
use App\Repositories\Interfaces\ClassroomRepositoryInterface;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use App\Repositories\Interfaces\SubjectRepositoryInterface;
use App\Repositories\Interfaces\TeacherRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\OrganizationRepository;
use App\Repositories\StudentRepository;
use App\Repositories\SubjectRepository;
use App\Repositories\TeacherRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrganizationRepositoryInterface::class, OrganizationRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TeacherRepositoryInterface::class, TeacherRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(ClassroomRepositoryInterface::class, ClassroomRepository::class);
        $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

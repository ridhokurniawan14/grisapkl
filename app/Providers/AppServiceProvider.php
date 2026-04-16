<?php

namespace App\Providers;

use App\Models\Dudika;
use App\Models\Student;
use App\Models\Teacher;
use App\Observers\StudentObserver;
use App\Observers\DudikaObserver;
use App\Observers\TeacherObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Student::observe(StudentObserver::class);
        Dudika::observe(DudikaObserver::class);
        Teacher::observe(TeacherObserver::class);
    }
}

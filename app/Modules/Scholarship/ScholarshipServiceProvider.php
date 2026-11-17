<?php

namespace App\Modules\Scholarship;

use Illuminate\Support\ServiceProvider;
use App\Modules\Scholarship\Domain\Contracts\ScholarshipRepository;
use App\Modules\Scholarship\Infrastructure\Repositories\EloquentScholarshipRepository;

class ScholarshipServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ScholarshipRepository::class,
            EloquentScholarshipRepository::class
        );
    }
}
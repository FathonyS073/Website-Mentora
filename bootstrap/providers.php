<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Modules\Identity\IdentityServiceProvider;
use App\Modules\Scholarship\ScholarshipServiceProvider;


return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    IdentityServiceProvider::class,
    ScholarshipServiceProvider::class,
    App\Modules\Event\EventServiceProvider::class,
    App\Modules\Article\ArticleServiceProvider::class,
];
<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\RouteBindingServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    RouteBindingServiceProvider::class,
];

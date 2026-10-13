<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Platform plugins
    |--------------------------------------------------------------------------
    |
    | Plugins are deliberately registered through configuration. A plugin can
    | be shipped as a Composer package or as a local module without changing
    | the platform core.
    |
    */
    'manifests' => [
        App\Modules\Grades\GradesPlugin::class,
    ],
];

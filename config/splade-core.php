<?php

return [

    'compiled_scripts' => env('SPLADE_COMPILED_SCRIPTS_PATH', resource_path('js/splade')),

    'prettify_compiled_scripts' => env('SPLADE_PRETTIFY_COMPILED_SCRIPTS', false),

    'invoke_component_uri' => '/_splade-core/invoke-component',

    'invoke_component_middleware' => ['web'],

];

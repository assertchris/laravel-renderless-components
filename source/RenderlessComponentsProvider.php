<?php

namespace RenderlessComponents;

use Illuminate\Support\ServiceProvider;

class RenderlessComponentsProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->singleton(
            'blade.compiler',
            fn($app) => new RenderlessComponentsBladeCompiler($app['files'], $app['config']['view.compiled']),
        );
    }
}

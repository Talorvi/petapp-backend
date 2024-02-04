<?php

namespace Tests\Utilities;

trait DisablesSearchSyncing
{
    public function setUpScoutSyncing(): void
    {
        // Disable Scout syncing for all models
        //\Illuminate\Support\Facades\Config::set('scout.driver', 'null');
    }

    public function tearDownScoutSyncing(): void
    {
        // Reset Scout driver after the test
        //\Illuminate\Support\Facades\Config::set('scout.driver', env('SCOUT_DRIVER'));
    }
}

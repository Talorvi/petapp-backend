<?php

namespace App\Traits;

namespace App\Traits;

trait ConditionallySearchable {
    use \Laravel\Scout\Searchable {
        \Laravel\Scout\Searchable::bootSearchable as scoutBootSearchable;
    }

    public static function bootConditionallySearchable()
    {
        if (!app()->runningUnitTests()) {
            static::scoutBootSearchable();
        }
    }
}

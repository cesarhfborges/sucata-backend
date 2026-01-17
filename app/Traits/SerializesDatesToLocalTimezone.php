<?php

namespace App\Traits;

use Carbon\Carbon;

trait SerializesDatesToLocalTimezone
{
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return Carbon::instance($date)
            ->setTimezone(config('app.timezone'))
            ->format('Y-m-d\TH:i:s');
    }
}

<?php

namespace App\Observers;

use App\Models\Attraction;
use Illuminate\Support\Facades\Cache;

class AttractionObserver
{
    /**
     * Handle the Attraction "created" event.
     *
     * @param  \App\Models\Attraction  $attraction
     * @return void
     */
    public function created(Attraction $attraction)
    {
        Cache::forget('attractions');
    }

    /**
     * Handle the Attraction "updated" event.
     *
     * @param  \App\Models\Attraction  $attraction
     * @return void
     */
    public function updated(Attraction $attraction)
    {
        //
    }

    /**
     * Handle the Attraction "deleted" event.
     *
     * @param  \App\Models\Attraction  $attraction
     * @return void
     */
    public function deleted(Attraction $attraction)
    {
        //
    }

    /**
     * Handle the Attraction "restored" event.
     *
     * @param  \App\Models\Attraction  $attraction
     * @return void
     */
    public function restored(Attraction $attraction)
    {
        //
    }

    /**
     * Handle the Attraction "force deleted" event.
     *
     * @param  \App\Models\Attraction  $attraction
     * @return void
     */
    public function forceDeleted(Attraction $attraction)
    {
        //
    }
}

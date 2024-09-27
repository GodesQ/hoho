<?php

namespace App\Observers;

use App\Models\TourReservation;

class TourReservationObserver
{
    /**
     * Handle the TourReservation "created" event.
     *
     * @param  \App\Models\TourReservation  $tourReservation
     * @return void
     */
    public function created(TourReservation $tourReservation)
    {
        //
    }

    /**
     * Handle the TourReservation "updated" event.
     *
     * @param  \App\Models\TourReservation  $tourReservation
     * @return void
     */
    public function updated(TourReservation $tourReservation)
    {
        //
    }

    /**
     * Handle the TourReservation "deleted" event.
     *
     * @param  \App\Models\TourReservation  $tourReservation
     * @return void
     */
    public function deleted(TourReservation $tourReservation)
    {
        //
    }

    /**
     * Handle the TourReservation "restored" event.
     *
     * @param  \App\Models\TourReservation  $tourReservation
     * @return void
     */
    public function restored(TourReservation $tourReservation)
    {
        //
    }

    /**
     * Handle the TourReservation "force deleted" event.
     *
     * @param  \App\Models\TourReservation  $tourReservation
     * @return void
     */
    public function forceDeleted(TourReservation $tourReservation)
    {
        //
    }
}

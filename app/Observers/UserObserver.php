<?php

namespace App\Observers;

use Careminate\Databases\Model;

class UserObserver
{
    /**
     * Handle the "created" event for the model.
     *
     * @param  \Careminate\Databases\Model  $model
     * @return void
     */
    public function created(Model $model)
    {
        // Handle the "created" event
    }

    /**
     * Handle the "updated" event for the model.
     *
     * @param  \Careminate\Databases\Model  $model
     * @return void
     */
    public function updated(Model $model)
    {
        // Handle the "updated" event
    }

    /**
     * Handle the "deleted" event for the model.
     *
     * @param  \Careminate\Databases\Model  $model
     * @return void
     */
    public function deleted(Model $model)
    {
        // Handle the "deleted" event
    }

    // Add more event handlers as needed (restored, force deleted, etc.)
}

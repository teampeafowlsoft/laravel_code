<?php

namespace Modules\ProductManagement\Events;

use Illuminate\Queue\SerializesModels;
use Modules\ProductManagement\Entities\ProductCartBooking;

class ProductCartBookingRequested
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $booking;

    public function __construct(ProductCartBooking $booking)
    {
        $this->booking = $booking;
    }


    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}

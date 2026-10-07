<?php

namespace App\Events;

use App\Models\Food;
use App\Models\Menu;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FoodCapacityFreedUp
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public Food $food ;
    public Menu $menu ;
    public int $freed_capacity;
    public bool $is_dailySale ;

    public function __construct(Food $food , Menu $menu , int $freed_capacity , bool $is_dailySale = false)
    {
        $this->food = $food ;
        $this->menu = $menu ;
        $this->freed_capacity = $freed_capacity;
        $this->is_dailySale= $is_dailySale;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}

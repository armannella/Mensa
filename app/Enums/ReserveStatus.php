<?php

namespace App\Enums;

enum ReserveStatus : string
{
    case ACTIVE = 'active';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    
}

<?php

namespace App\Enums;

enum ReserveStatus : string
{
    case ACTIVE = 'pending';
    case DELIVERED = 'delivered';
    case MISSED = 'missed';
      
}

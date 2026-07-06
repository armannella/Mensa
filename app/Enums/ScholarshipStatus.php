<?php

namespace App\Enums;

enum ScholarshipStatus:string
{
    case PENDING = 'pending' ;
    case REJECTED = 'rejected';
    case APPROVED = 'approved';
}

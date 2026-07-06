<?php

namespace App\Enums;

enum UserRole :string
{
    case ADMIN = 'admin' ;
    case MENSA = 'mensa';
    case STUDENT = 'student';
}

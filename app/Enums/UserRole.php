<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case FarmOwner = 'farm_owner';
}

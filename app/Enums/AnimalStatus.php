<?php

namespace App\Enums;

enum AnimalStatus: string
{
    case Alive = 'alive';
    case Sold = 'sold';
    case Deceased = 'deceased';
}

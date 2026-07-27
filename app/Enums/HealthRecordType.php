<?php

namespace App\Enums;

enum HealthRecordType: string
{
    case Vaccination = 'vaccination';
    case Treatment = 'treatment';
    case VetVisit = 'vet_visit';
}

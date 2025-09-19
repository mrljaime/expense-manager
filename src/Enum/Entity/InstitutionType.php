<?php

namespace App\Enum\Entity;

enum InstitutionType: string
{
    case BANK = 'bank';
    case APP = 'app';
    case APP_CRYPTO = 'app_crypto';
}

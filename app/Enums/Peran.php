<?php

namespace App\Enums;

enum Peran: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case User = 'user';
}

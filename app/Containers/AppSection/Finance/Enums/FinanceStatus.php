<?php

namespace App\Containers\AppSection\Finance\Enums;

enum FinanceStatus: string
{
    case PLANNED = 'planned';
    case PAID = 'paid';
    case PENDING = 'pending';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';
}

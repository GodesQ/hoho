<?php
namespace Enum;

enum TransactionStatusEnum:string
{
    case SUCCESS = 'success';
    case PENDING = 'pending';
    case INCOMPLETE = 'inc';
    case CANCELLED = 'cancelled';
}
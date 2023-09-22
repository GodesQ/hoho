<?php
namespace Enum;

enum ReservationStatusEnum:string
{
    case APPROVED = 'approved';
    case DONE = 'done';
    case CANCELLED = 'cancelled';
    case PENDING = 'pending';
}
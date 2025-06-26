<?php

namespace App\Enums;

enum AssignmentStatus: string
{
    case UNSTARTED = 'unstarted';
    case SUBMITTED = 'submitted';
    case IN_PROGRESS = 'in_progress';
    case GRADED = 'graded';
}

<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Lab404\Impersonate\Events\TakeImpersonation;
use Lab404\Impersonate\Events\LeaveImpersonation;

class LogImpersonation
{
    public function handleTake(TakeImpersonation $event): void
    {
        AuditLog::create([
            'subject_type' => get_class($event->impersonated),
            'subject_id' => $event->impersonated->getKey(),
            'action' => 'impersonation_started',
            'changes' => [
                'impersonator_id' => $event->impersonator->getKey(),
                'impersonated_id' => $event->impersonated->getKey(),
            ],
            'user_id' => $event->impersonator->getKey(),
        ]);
    }

    public function handleLeave(LeaveImpersonation $event): void
    {
        AuditLog::create([
            'subject_type' => get_class($event->impersonated),
            'subject_id' => $event->impersonated->getKey(),
            'action' => 'impersonation_stopped',
            'changes' => [
                'impersonator_id' => $event->impersonator->getKey(),
                'impersonated_id' => $event->impersonated->getKey(),
            ],
            'user_id' => $event->impersonator->getKey(),
        ]);
    }
}


<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Jobs\ProcessTaskNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendTaskNotification implements ShouldQueue
{
    public function handle(TaskCompleted $event): void
    {
        ProcessTaskNotification::dispatch($event->task);
    }
}

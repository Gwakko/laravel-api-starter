<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class ProcessTaskNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Task $task,
    ) {}

    public function handle(): void
    {
        // TODO: Send notification via email/Slack/Telegram
        Log::info('Task completed notification', [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'user_id' => $this->task->user_id,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class TaskCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Task $task,
    ) {}
}

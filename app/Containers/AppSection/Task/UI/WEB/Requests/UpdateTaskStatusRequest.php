<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Enum;

final class UpdateTaskStatusRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        $task = Task::find($this->route('id'));

        return null === $task || $this->user()->can('update', $task);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(TaskStatus::class)],
        ];
    }
}

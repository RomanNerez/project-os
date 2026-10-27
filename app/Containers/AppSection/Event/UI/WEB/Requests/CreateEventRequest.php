<?php

namespace App\Containers\AppSection\Event\UI\WEB\Requests;

use App\Containers\AppSection\Event\Enums\EventType;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

/**
 * Access is checked in PrepareEventDataTask: the project must be visible to the user
 * and every participant must be a candidate for this event.
 */
class CreateEventRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::enum(EventType::class)],
            'color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'all_day' => ['required', 'boolean'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'reminder_minutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
            'participant_ids' => ['nullable', 'array', 'max:100'],
            'participant_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'project_id' => 'проєкт',
            'title' => 'назва',
            'location' => 'місце',
            'start_at' => 'початок',
            'end_at' => 'кінець',
            'reminder_minutes' => 'нагадування',
            'participant_ids' => 'учасники',
        ];
    }
}

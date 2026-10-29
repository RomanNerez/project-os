<?php

namespace App\Containers\AppSection\TimeEntry\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class UpdateTimeEntryRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('project'));
    }

    public function rules(): array
    {
        return [
            'description' => ['sometimes', 'required', 'string', 'max:255'],
            'started_at' => ['required', 'date'],
            'stopped_at' => ['required', 'date', 'after:started_at'],
        ];
    }

    public function attributes(): array
    {
        return [
            'description' => 'назва',
            'started_at' => 'початок',
            'stopped_at' => 'кінець',
        ];
    }
}

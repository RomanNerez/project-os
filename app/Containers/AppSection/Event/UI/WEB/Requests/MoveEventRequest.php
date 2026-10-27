<?php

namespace App\Containers\AppSection\Event\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class MoveEventRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('event'));
    }

    public function rules(): array
    {
        return [
            'all_day' => ['required', 'boolean'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
        ];
    }
}

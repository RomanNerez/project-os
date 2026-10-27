<?php

namespace App\Containers\AppSection\Event\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

/**
 * The range FullCalendar asks for; visibility is scoped in EventRepository.
 */
final class ListEventsRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
            'search' => ['nullable', 'string', 'max:512'],
            'searchJoin' => ['nullable', 'string', 'in:and,or'],
        ];
    }
}

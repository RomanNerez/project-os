<?php

namespace App\Containers\AppSection\Event\UI\WEB\Requests;

final class UpdateEventRequest extends CreateEventRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('event'));
    }
}

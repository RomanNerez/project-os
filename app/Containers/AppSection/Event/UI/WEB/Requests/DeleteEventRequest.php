<?php

namespace App\Containers\AppSection\Event\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class DeleteEventRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('event'));
    }

    public function rules(): array
    {
        return [];
    }
}

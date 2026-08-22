<?php

namespace App\Containers\AppSection\TimeEntry\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class StopTimeEntryRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [];
    }
}

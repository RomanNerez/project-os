<?php

namespace App\Containers\AppSection\Project\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class ListProjectsRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:512'],
            'searchJoin' => ['nullable', 'string', 'in:and,or'],
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'in:10,20,50'],
        ];
    }
}

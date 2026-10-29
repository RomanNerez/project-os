<?php

namespace App\Containers\AppSection\Media\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class DeleteProjectTaskMediaRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('media'));
    }

    public function rules(): array
    {
        return [];
    }
}

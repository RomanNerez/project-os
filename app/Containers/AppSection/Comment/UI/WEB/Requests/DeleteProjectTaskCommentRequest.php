<?php

namespace App\Containers\AppSection\Comment\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class DeleteProjectTaskCommentRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('comment'));
    }

    public function rules(): array
    {
        return [];
    }
}

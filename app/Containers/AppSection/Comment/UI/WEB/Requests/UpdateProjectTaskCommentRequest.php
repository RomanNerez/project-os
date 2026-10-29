<?php

namespace App\Containers\AppSection\Comment\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class UpdateProjectTaskCommentRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('comment'));
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string']
        ];
    }
}

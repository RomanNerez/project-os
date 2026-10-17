<?php

namespace App\Containers\AppSection\Comment\UI\WEB\Requests;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class UpdateCommentRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('update', Comment::query()->findOrFail((int) $this->route('id')));
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string']
        ];
    }
}

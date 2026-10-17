<?php

namespace App\Containers\AppSection\Media\UI\WEB\Requests;

use App\Containers\AppSection\Media\Models\Media;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class DeleteMediaRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('delete', Media::query()->findOrFail((int) $this->route('id')));
    }

    public function rules(): array
    {
        return [];
    }
}

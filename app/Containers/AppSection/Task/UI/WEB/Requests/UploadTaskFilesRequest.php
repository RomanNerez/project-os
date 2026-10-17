<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class UploadTaskFilesRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('uploadFiles', Task::query()->findOrFail((int) $this->route('id')));
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'files'   => ['required', 'array'],
            'files.*' => ['file'],
        ];
    }
}

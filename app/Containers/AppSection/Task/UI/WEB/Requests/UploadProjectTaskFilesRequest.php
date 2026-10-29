<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class UploadProjectTaskFilesRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('uploadFiles', $this->route('task'));
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

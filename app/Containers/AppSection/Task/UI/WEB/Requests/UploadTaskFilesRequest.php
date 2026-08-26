<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class UploadTaskFilesRequest extends ParentRequest
{
    protected array $decode = [];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'files' => ['required'],
        ];
    }
}

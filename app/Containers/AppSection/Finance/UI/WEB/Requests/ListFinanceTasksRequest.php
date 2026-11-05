<?php

namespace App\Containers\AppSection\Finance\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class ListFinanceTasksRequest extends ParentRequest
{
    protected array $decode = [];

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('project'));
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'except_finance_id' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer', 'in:10,20,50'],
        ];
    }
}

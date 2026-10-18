<?php

namespace App\Containers\AppSection\Finance\UI\WEB\Requests;

final class UpdateFinanceRequest extends CreateFinanceRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('finance'));
    }

    public function rules(): array
    {
        return [
            ...parent::rules(),
            'removed_receipt_ids' => ['nullable', 'array'],
            'removed_receipt_ids.*' => ['integer'],
        ];
    }
}

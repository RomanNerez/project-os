<?php

namespace App\Containers\AppSection\Finance\UI\WEB\Requests;

use App\Containers\AppSection\Finance\Enums\FinanceCurrency;
use App\Containers\AppSection\Finance\Enums\FinanceStatus;
use App\Containers\AppSection\Finance\Enums\FinanceType;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

/**
 * Access is checked in PrepareFinanceDataTask: the project must be visible to the user
 * and every task must belong to it.
 */
class CreateFinanceRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', Rule::enum(FinanceType::class)],
            'currency' => ['required', Rule::enum(FinanceCurrency::class)],
            'amount' => ['required', 'numeric', 'gt:0', 'max:999999999999'],
            'exchange_rate' => ['nullable', 'required_if:currency,' . FinanceCurrency::USD->value, 'numeric', 'gt:0', 'max:99999999'],
            'status' => ['required', Rule::enum(FinanceStatus::class)],
            'due_date' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'task_ids' => ['nullable', 'array'],
            'task_ids.*' => ['integer', 'distinct', 'exists:tasks,id'],
            'receipts' => ['nullable', 'array', 'max:10'],
            'receipts.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    public function attributes(): array
    {
        return [
            'project_id' => 'проєкт',
            'title' => 'назва',
            'amount' => 'сума',
            'exchange_rate' => 'курс',
            'task_ids' => 'задачі',
            'receipts.*' => 'квитанція',
        ];
    }
}

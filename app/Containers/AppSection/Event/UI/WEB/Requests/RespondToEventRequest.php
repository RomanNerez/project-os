<?php

namespace App\Containers\AppSection\Event\UI\WEB\Requests;

use App\Containers\AppSection\Event\Enums\EventParticipantStatus;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

final class RespondToEventRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('respond', $this->route('event'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(EventParticipantStatus::class)->except(EventParticipantStatus::PENDING)],
        ];
    }
}

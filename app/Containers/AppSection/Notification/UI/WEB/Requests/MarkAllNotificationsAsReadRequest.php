<?php

namespace App\Containers\AppSection\Notification\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class MarkAllNotificationsAsReadRequest extends ParentRequest
{
    protected array $decode = [];

    /**
     * Notifications are always scoped to the current user, so any authenticated user may proceed.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}

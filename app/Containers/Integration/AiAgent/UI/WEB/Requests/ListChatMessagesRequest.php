<?php

namespace App\Containers\Integration\AiAgent\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class ListChatMessagesRequest extends ParentRequest
{
    protected array $decode = [];
    
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}

<?php

namespace App\Ship\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Inertia\ProvidesScrollMetadata;

final class PaginatorScrollMetadata implements ProvidesScrollMetadata
{
    public function __construct(private LengthAwarePaginator $paginator) {}

    public function getPageName(): string
    {
        return $this->paginator->getPageName();
    }

    public function getPreviousPage(): int|string|null
    {
        return $this->paginator->currentPage() > 1
            ? $this->paginator->currentPage() - 1
            : null;
    }

    public function getNextPage(): int|string|null
    {
        return $this->paginator->hasMorePages()
            ? $this->paginator->currentPage() + 1
            : null;
    }

    public function getCurrentPage(): int|string|null
    {
        return $this->paginator->currentPage();
    }
}
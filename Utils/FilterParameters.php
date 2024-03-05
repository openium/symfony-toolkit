<?php

namespace Openium\SymfonyToolKitBundle\Utils;

class FilterParameters
{
    private readonly ?string $search;

    private readonly ?int $page;

    private readonly ?int $limit;

    private readonly ?string $order;

    private readonly ?string $orderBy;

    public function __construct(
        ?string $search = null,
        ?int $page = null,
        ?int $limit = null,
        ?string $order = null,
        ?string $orderBy = null
    ) {
        $this->search = $search;
        $this->page = $page;
        // if client send page without limit, set default limit to 10 items
        if ($page !== null && $limit === null) {
            $this->limit = 10;
        } else {
            $this->limit = $limit;
        }
        $this->orderBy = $orderBy;
        // if client send orderBy without order, set default order to ASC
        if ($orderBy !== null && $order === null) {
            $this->order = 'ASC';
        } else {
            $this->order = $order === null ? null : strtoupper($order);
        }
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }
}

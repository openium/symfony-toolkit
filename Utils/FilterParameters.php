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
        if ($page !== null && $page < 1) {
            $page = 1;
        }
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

    public function getOffset(): ?int
    {
        return ($this->getPage() !== null && $this->getLimit() !== null)
            ? ($this->getPage() - 1) * $this->getLimit()
            : null;
    }

    /**
     * getHash
     * return sha1 string who contains whole filters
     * can be useful for cache name
     *
     * @return string
     */
    public function getHash(): string
    {
        return sha1(
            sprintf(
                '%s%s%s%s%s',
                $this->search ?? '',
                $this->order ?? '',
                $this->page ?? '',
                $this->limit ?? '',
                $this->orderBy ?? ''
            )
        );
    }
}

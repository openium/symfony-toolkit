<?php

namespace Openium\SymfonyToolKitBundle\Utils;

class FilterParameters
{
    private readonly ?int $page;

    private readonly ?int $limit;

    private readonly ?string $order;

    public function __construct(
        private readonly ?string $search = null,
        ?int $page = null,
        ?int $limit = null,
        ?string $order = null,
        private readonly ?string $orderBy = null
    ) {
        $this->page = $page ?? 1;
        if ($page !== null && $page < 1) {
            $page = 1;
        }

        // if client send page without limit, set default limit to 10 items
        $this->limit = $page !== null && $limit === null ? 10 : $limit;
        // if client send orderBy without order, set default order to ASC
        if ($this->orderBy !== null && $order === null) {
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

<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use Symfony\Component\Serializer\Attribute\Groups;

class PaginatedResult
{
    #[Groups(['default'])]
    private int $page;

    #[Groups(['default'])]
    private int $totalPage;

    #[Groups(['default'])]
    private ?int $limit;

    #[Groups(['default'])]
    private array $data;

    public function __construct(array $data, FilterParameters $filterParameters, int $totalItems)
    {
        $this->limit = $filterParameters->getLimit();
        $this->page = $filterParameters->getPage() ?? 1;
        $this->totalPage = $this->limit !== null ? ceil($totalItems / $this->limit) : 1;
        $this->data = $data;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function getTotalPage(): int
    {
        return $this->totalPage;
    }

    public function setTotalPage(int $totalPage): self
    {
        $this->totalPage = $totalPage;
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }
}

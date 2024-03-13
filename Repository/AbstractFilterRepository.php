<?php

namespace Openium\SymfonyToolKitBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Openium\SymfonyToolKitBundle\Utils\FilterParameters;

/**
 * Class AbstractFilterRepository
 *
 * @package Openium\SymfonyToolKitBundle\Repository
 */
abstract class AbstractFilterRepository extends ServiceEntityRepository
{
    /**
     * applyFilters
     *
     * @param QueryBuilder $qb
     * @param string $alias
     * @param FilterParameters $filterParameters
     *
     * @return void
     */
    protected function applyFilters(QueryBuilder $qb, string $alias, FilterParameters $filterParameters): void
    {
        if ($filterParameters->getOrderBy() !== null) {
            $qb->orderBy($alias . '.' . $filterParameters->getOrderBy(), $filterParameters->getOrder());
        }
        if ($filterParameters->getOffset() !== null) {
            $qb->setMaxResults($filterParameters->getLimit());
            $qb->setFirstResult($filterParameters->getOffset());
        }
    }
}

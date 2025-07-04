<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use Doctrine\ORM\QueryBuilder;

/**
 * Class FilterRepositoryUtils
 *
 * @package Openium\SymfonyToolKitBundle\Utils
 */
class FilterRepositoryUtils
{
    /**
     * applyFilters
     *
     *
     */
    public static function applyFilters(
        QueryBuilder $qb,
        string $alias,
        FilterParameters $filterParameters
    ): void {
        if ($filterParameters->getOrderBy() !== null) {
            $qb->orderBy(
                $alias . '.' . $filterParameters->getOrderBy(),
                $filterParameters->getOrder()
            );
        }

        if ($filterParameters->getOffset() !== null) {
            $qb->setMaxResults($filterParameters->getLimit());
            $qb->setFirstResult($filterParameters->getOffset());
        }
    }
}

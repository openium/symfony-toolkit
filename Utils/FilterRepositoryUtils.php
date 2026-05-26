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
     */
    public static function applyFilters(
        QueryBuilder $queryBuilder,
        string $alias,
        FilterParameters $filterParameters
    ): void {
        if ($filterParameters->getOrderBy() !== null) {
            $queryBuilder->orderBy(
                $alias . '.' . $filterParameters->getOrderBy(),
                $filterParameters->getOrder()
            );
        }

        if ($filterParameters->getOffset() !== null) {
            $queryBuilder->setMaxResults($filterParameters->getLimit());
            $queryBuilder->setFirstResult($filterParameters->getOffset());
        }
    }
}

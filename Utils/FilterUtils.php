<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use Symfony\Component\HttpFoundation\Request;

class FilterUtils
{
    public const SEARCH_PARAMETER = 'search';
    public const PAGE_PARAMETER = 'page';
    public const LIMIT_PARAMETER = 'limit';
    public const ORDER_PARAMETER = 'order';
    public const ORDER_BY_PARAMETER = 'order-by';

    /**
     * generateFromRequest
     *
     * @param Request $request
     *
     * @return FilterParameters
     */
    public static function generateFromRequest(Request $request): FilterParameters
    {
        $search = $request->query->has(self::SEARCH_PARAMETER)
            ? $request->query->getString(self::SEARCH_PARAMETER)
            : null;
        $page = $request->query->has(self::PAGE_PARAMETER)
            ? $request->query->getInt(self::PAGE_PARAMETER)
            : null;
        $limit = $request->query->has(self::LIMIT_PARAMETER)
            ? $request->query->getInt(self::LIMIT_PARAMETER)
            : null;
        $order = $request->query->has(self::ORDER_PARAMETER)
            ? $request->query->getString(self::ORDER_PARAMETER)
            : null;
        $orderBy = $request->query->has(self::ORDER_BY_PARAMETER)
            ? $request->query->getString(self::ORDER_BY_PARAMETER)
            : null;
        return new FilterParameters(
            $search,
            $page,
            $limit,
            $order,
            $orderBy
        );
    }
}

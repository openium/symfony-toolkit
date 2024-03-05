<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use Symfony\Component\HttpFoundation\Request;

class FilterUtils
{
    /**
     * generateFromRequest
     *
     * @param Request $request
     *
     * @return FilterParameters
     */
    public static function generateFromRequest(Request $request): FilterParameters
    {
        $search = $request->query->has('search') ? $request->query->getString('search') : null;
        $page = $request->query->has('page') ? $request->query->getInt('page') : null;
        $limit = $request->query->has('limit') ? $request->query->getInt('limit') : null;
        $order = $request->query->has('order') ? $request->query->getString('order') : null;
        $orderBy = $request->query->has('order-by') ? $request->query->getString('order-by') : null;
        return new FilterParameters(
            $search,
            $page,
            $limit,
            $order,
            $orderBy
        );
    }
}

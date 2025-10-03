<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use BackedEnum;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\DBAL\Logging\Middleware;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class DebugUtils
{
    private static ?\DateTimeZone $utcTz = null;

    public static function setDoctrineQueryLogger(EntityManagerInterface $entityManager): void
    {
        $logger = new Logger('sql');
        $logger->pushHandler(new StreamHandler('php://stdout'));
        // Create the middleware
        $loggingMiddleware = new Middleware($logger);
        $configuration = $entityManager->getConnection()->getConfiguration();
        // Add the middleware into the configuration
        $configuration->setMiddlewares([$loggingMiddleware]);
    }

    public static function logDoctrineQuery(Query $query): string
    {
        $sql = $query->getSQL();
        $dql = $query->getDQL();
        // 1. Retrieves the parameter names in the order they appear in the DQL
        preg_match_all('/:([a-zA-Z0-9_]+)/', $dql, $matches);
        $orderedParamNames = $matches[1];
        // 2. Associates each name with its formatted value.
        $params = [];
        foreach ($query->getParameters() as $param) {
            $params[$param->getName()] = self::formatSqlParam($param->getValue());
        }
        // 3. Builds an ordered list of values.
        $orderedValues = array_map(fn($name) => $params[$name], $orderedParamNames);
        // 4. Injects the values into the SQL by replacing each ? one by one.
        foreach ($orderedValues as $val) {
            $sql = preg_replace('/\?/', $val, $sql, 1);
        }
        return $sql;
    }

    public static function formatSqlParam($value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        if ($value instanceof DateTimeInterface) {
            if (self::$utcTz === null) {
                self::$utcTz = new DateTimeZone('UTC');
            }
            $utc = (clone $value)->setTimezone(self::$utcTz);
            return "'" . $utc->format('Y-m-d H:i:s') . "'";
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if ($value instanceof BackedEnum) {
            return self::formatSqlParam($value->value);
        }
        if (is_object($value) && method_exists($value, 'getId')) {
            return is_numeric($value->getId())
                ? $value->getId()
                : "'" . addslashes($value->getId()) . "'";
        }
        if (is_array($value) || $value instanceof \Traversable) {
            $items = array_map(
                fn($v) => self::formatSqlParam($v),
                is_array($value) ? $value : iterator_to_array($value)
            );
            return implode(',', $items);
        }
        if (is_numeric($value)) {
            return $value;
        }
        return "'" . addslashes((string)$value) . "'";
    }
}

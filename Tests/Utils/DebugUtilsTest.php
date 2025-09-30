<?php

namespace Openium\SymfonyToolKitBundle\Tests\Utils;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Openium\SymfonyToolKitBundle\Utils\DebugUtils;
use PHPUnit\Framework\TestCase;

class DebugUtilsTest extends TestCase
{
    public function testFormatSqlParamWithNumeric(): void
    {
        $this->assertSame('123', DebugUtils::formatSqlParam(123));
    }

    public function testFormatSqlParamWithString(): void
    {
        $this->assertSame("'abc'", DebugUtils::formatSqlParam('abc'));
    }

    public function testFormatSqlParamWithArray(): void
    {
        $this->assertSame("1,'a'", DebugUtils::formatSqlParam([1, 'a']));
    }
    public function testFormatSqlParamWithObjectHavingGetId()
    {
        $obj = new class {
            public function getId()
            {
                return 42;
            }
        };
        $this->assertSame('42', DebugUtils::formatSqlParam($obj));
    }
    public function testFormatSqlParamWithObjectHavingGetIdString()
    {
        $obj = new class {
            public function getId()
            {
                return 'foo';
            }
        };
        $this->assertSame("'foo'", DebugUtils::formatSqlParam($obj));
    }

    public function testLogDoctrineQueryInjectsValues()
    {
        $queryMock = $this->createMock(Query::class);
        $queryMock->method('getSQL')->willReturn('SELECT * FROM user WHERE id = ? AND name = ?');
        $queryMock->method('getDQL')->willReturn('SELECT u FROM User u WHERE u.id = :id AND u.name = :name');

        $param1 = $this->getMockBuilder('Doctrine\ORM\Query\Parameter')
            ->disableOriginalConstructor()
            ->getMock();
        $param1->method('getName')->willReturn('id');
        $param1->method('getValue')->willReturn(5);

        $param2 = $this->getMockBuilder('Doctrine\ORM\Query\Parameter')
            ->disableOriginalConstructor()
            ->getMock();
        $param2->method('getName')->willReturn('name');
        $param2->method('getValue')->willReturn('Roger');

        $queryMock->method('getParameters')->willReturn(new ArrayCollection([$param1, $param2]));

        $result = DebugUtils::logDoctrineQuery($queryMock);
        $this->assertSame("SELECT * FROM user WHERE id = 5 AND name = 'Roger'", $result);
    }

    public function testSetDoctrineQueryLoggerDoesNotThrow()
    {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $connectionMock = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $configurationMock = $this->getMockBuilder('Doctrine\DBAL\Configuration')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManagerMock->method('getConnection')->willReturn($connectionMock);
        $connectionMock->method('getConfiguration')->willReturn($configurationMock);
        $configurationMock->expects($this->once())->method('setMiddlewares');

        DebugUtils::setDoctrineQueryLogger($entityManagerMock);
        $this->assertTrue(true); // Si aucune exception, le test passe
    }
}

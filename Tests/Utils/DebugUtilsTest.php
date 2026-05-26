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

    public function testFormatSqlParamWithObjectHavingGetId(): void
    {
        $obj = new class {
            public function getId() { return 42; }
        };
        $this->assertSame('42', DebugUtils::formatSqlParam($obj));
    }

    public function testFormatSqlParamWithDateTimeImmutable(): void
    {
        $dt = new \DateTimeImmutable('2025-10-01 14:30:00');
        $this->assertSame(
            "'2025-10-01 14:30:00'",
            DebugUtils::formatSqlParam($dt)
        );
    }

    public function testFormatSqlParamWithDateTime(): void
    {
        $dt = new \DateTime('2025-12-31 23:59:59');
        $this->assertSame(
            "'2025-12-31 23:59:59'",
            DebugUtils::formatSqlParam($dt)
        );
    }

    public function testFormatSqlParamWithDateTimeIgnoresTimezone(): void
    {
        $dt = new \DateTime('2025-10-01 14:30:00', new \DateTimeZone('UTC'));
        $dtParis = $dt->setTimezone(new \DateTimeZone('Europe/Paris'));
        // Le format Y-m-d H:i:s ne change pas, peu importe le timezone interne
        $this->assertSame(
            "'2025-10-01 14:30:00'",
            DebugUtils::formatSqlParam($dt)
        );
        $this->assertSame(
            "'2025-10-01 14:30:00'",
            DebugUtils::formatSqlParam($dtParis)
        );
    }

    public function testFormatSqlParamWithDateTimeDropsMicroseconds(): void
    {
        $dt = \DateTime::createFromFormat('Y-m-d H:i:s.u', '2025-10-01 14:30:00.123456');
        $this->assertSame(
            "'2025-10-01 14:30:00'",
            DebugUtils::formatSqlParam($dt)
        );
    }

    public function testFormatSqlParamWithObjectHavingGetIdString(): void
    {
        $obj = new class {
            public function getId() { return 'foo'; }
        };
        $this->assertSame("'foo'", DebugUtils::formatSqlParam($obj));
    }

    public function testFormatSqlParamWithNull(): void
    {
        $this->assertSame('NULL', DebugUtils::formatSqlParam(null));
    }

    public function testFormatSqlParamWithBoolean(): void
    {
        $this->assertSame('1', DebugUtils::formatSqlParam(true));
        $this->assertSame('0', DebugUtils::formatSqlParam(false));
    }

    public function testFormatSqlParamWithEnum(): void
    {
        $this->assertSame("'foo'", DebugUtils::formatSqlParam(TestEnum::FOO));
    }

    public function testFormatSqlParamWithTraversable(): void
    {
        $coll = new \ArrayIterator([1, 'a']);
        $this->assertSame("1,'a'", DebugUtils::formatSqlParam($coll));
    }

    public function testFormatSqlParamWithSpecialCharactersString(): void
    {
        $this->assertSame("'O\\'Reilly'", DebugUtils::formatSqlParam("O'Reilly"));
    }

    public function testLogDoctrineQueryInjectsValues(): void
    {
        $queryMock = $this->createMock(Query::class);
        $queryMock->expects($this->once())->method('getSQL')->willReturn('SELECT * FROM user WHERE id = ? AND name = ?');
        $queryMock->expects($this->once())->method('getDQL')->willReturn(
            'SELECT u FROM User u WHERE u.id = :id AND u.name = :name'
        );
        $param1 = $this->getMockBuilder(\Doctrine\ORM\Query\Parameter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $param1->expects($this->once())->method('getName')->willReturn('id');
        $param1->expects($this->once())->method('getValue')->willReturn(5);
        $param2 = $this->getMockBuilder(\Doctrine\ORM\Query\Parameter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $param2->expects($this->once())->method('getName')->willReturn('name');
        $param2->expects($this->once())->method('getValue')->willReturn('Roger');
        $queryMock->method('getParameters')->willReturn(new ArrayCollection([$param1, $param2]));
        $result = DebugUtils::logDoctrineQuery($queryMock);
        $this->assertSame("SELECT * FROM user WHERE id = 5 AND name = 'Roger'", $result);
    }

    public function testSetDoctrineQueryLoggerDoesNotThrow(): void
    {
        $configurationMock = $this->getMockBuilder(\Doctrine\DBAL\Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configurationMock->expects(self::once())->method('setMiddlewares');
        $connectionMock = $this->getMockBuilder(\Doctrine\DBAL\Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock->expects(self::once())
            ->method('getConfiguration')
            ->willReturn($configurationMock);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects(self::once())
            ->method('getConnection')
            ->willReturn($connectionMock);
        DebugUtils::setDoctrineQueryLogger($entityManagerMock);
        $this->assertTrue(true); // Si aucune exception, le test passe
    }
}

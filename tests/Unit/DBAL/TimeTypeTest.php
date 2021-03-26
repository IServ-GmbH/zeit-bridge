<?php

declare(strict_types=1);

namespace IServ\Bridge\Zeit\Tests\Unit\DBAL;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Types\ConversionException;
use IServ\Bridge\Zeit\DBAL\TimeType;
use IServ\Library\Zeit\Time;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IServ\Bridge\Zeit\DBAL\TimeType
 * @uses \IServ\Library\Zeit\Time
 */
final class TimeTypeTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreate(): void
    {
        $type = new TimeType();
        $platform = $this->getPlatform();

        $time = new Time('11:42:11');

        $this->assertSame('zeit_time', $type->getName());
        $this->assertSame('TIME(0) WITHOUT TIME ZONE', $type->getSQLDeclaration([], $platform));
        $this->assertSame('11:42:11', $type->convertToDatabaseValue($time, $platform));
        $this->assertEquals($time, $type->convertToPHPValue('11:42:11', $platform));

        $this->assertNull($type->convertToPHPValue(null, $platform));
        $this->assertNull($type->convertToDatabaseValue(null, $platform));
        $this->assertSame($time, $type->convertToPHPValue($time, $platform));
    }

    public function testConvertToPhpValueFailsWithBadType(): void
    {
        $type = new TimeType();
        $platform = $this->getPlatform();

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue(new \DateTimeImmutable(), $platform);
    }

    public function testConvertToPhpValueFailsWithBadData(): void
    {
        $type = new TimeType();
        $platform = $this->getPlatform();

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue('no valid time string', $platform);
    }

    private function getPlatform(): AbstractPlatform
    {
        return new PostgreSQL100Platform();
    }
}

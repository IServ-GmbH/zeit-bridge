<?php

declare(strict_types=1);

namespace IServ\Bridge\Zeit\Tests\Unit\DBAL;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use IServ\Bridge\Zeit\DBAL\DateType;
use IServ\Library\Zeit\Date;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IServ\Bridge\Zeit\DBAL\DateType
 * @uses \IServ\Library\Zeit\Date
 */
final class DateTypeTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreate(): void
    {
        $type = new DateType();
        $platform = $this->getPlatform();

        $date = new Date('2021-04-22');

        $this->assertSame('zeit_date', $type->getName());
        $this->assertSame('DATE', $type->getSQLDeclaration([], $platform));
        $this->assertSame('2021-04-22', $type->convertToDatabaseValue($date, $platform));
        $this->assertEquals($date, $type->convertToPHPValue('2021-04-22', $platform));

        $this->assertNull($type->convertToPHPValue(null, $platform));
        $this->assertNull($type->convertToDatabaseValue(null, $platform));
        $this->assertSame($date, $type->convertToPHPValue($date, $platform));
    }

    public function testConvertToPhpValueFailsWithBadType(): void
    {
        $type = new DateType();
        $platform = $this->getPlatform();

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue(new \DateTimeImmutable(), $platform);
    }

    public function testConvertToPhpValueFailsWithBadData(): void
    {
        $type = new DateType();
        $platform = $this->getPlatform();

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue('no valid date string', $platform);
    }

    /**
     * @throws Exception
     */
    public function testFixBeforeConvertToDatabaseValue(): void
    {
        $type = new DateType();
        $platform = $this->getPlatform();

        $date = new Date('0000-04-22');
        $this->assertSame('0001-04-22', $type->convertToDatabaseValue($date, $platform));
    }

    /**
     * @throws Exception
     */
    public function testFixBeforeConvertToPHPValue(): void
    {
        $type = new DateType();
        $platform = $this->getPlatform();

        $date = new Date('9999-01-01');
        $this->assertEquals($date, $type->convertToPHPValue('10001-01-01', $platform));

        $date = new Date('-0001-01-01');
        $this->assertEquals($date, $type->convertToPHPValue('0001-01-01 BC', $platform));
    }

    private function getPlatform(): AbstractPlatform
    {
        /** @noinspection PhpUndefinedClassInspection */
        if (class_exists(PostgreSQL100Platform::class)) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return new PostgreSQL100Platform();
        }

        return new PostgreSQLPlatform();
    }
}

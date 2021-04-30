<?php

declare(strict_types=1);

namespace IServ\Bridge\Zeit\DBAL;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use IServ\Library\Zeit\Date;

final class DateType extends Type
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'zeit_date';
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Date) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', Date::class]);
        }

        $value = $this->fixBeforeConvertToDatabaseValue($value);

        return $value->toDateTime()->format($platform->getDateFormatString());
    }

    /**
     * {@inheritDoc}
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value || $value instanceof Date) {
            return $value;
        }

        if (!is_string($value)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'string']);
        }

        $value = $this->fixBeforeConvertToPHPValue($value);

        // PHP cannot create DT from format with BC dates, so we use an AC DateTime and create a BC Date from is. #meh
        $isBC = false;
        if ('-' === $value[0]) {
            $value = substr($value, 1);
            $isBC = true;
        }

        $dateTime = \DateTimeImmutable::createFromFormat('!' . $platform->getDateFormatString(), $value);

        if (!$dateTime) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateFormatString());
        }

        try {
            return Date::fromParts(($isBC ? '-' : '') . $dateTime->format('Y'), $dateTime->format('m'), $dateTime->format('d'));
        } catch (\InvalidArgumentException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    /**
     * Run to handle bad data before sending to the database
     */
    protected function fixBeforeConvertToDatabaseValue(Date $value): Date
    {
        // We may encounter a year "0" in PHP which does not exist.
        // The input is probably already broken, so we just add year to resolve this somehow.
        if ($value->toDateTime()->format('Y') === '0000') {
            return Date::fromDateTime($value->toDateTime()->modify('+1 year'));
        }

        return $value;
    }

    /**
     * Run to handle bad data coming from the database
     */
    protected function fixBeforeConvertToPHPValue(string $value): string
    {
        // Postgres: 0001-01-01 BC => PHP: -0001-01-01
        if (substr($value, -3) === ' BC') {
            return '-' . substr($value, 0, -3);
        }

        // Postgres: 10001-01-01 => PHP: 9999-01-01
        if (strpos($value, '-') === 5) {
            return substr_replace($value, '9999', 0, 5);
        }

        return $value;
    }
}

<?php

declare(strict_types=1);

namespace IServ\Bridge\Zeit\Form\Type;

use IServ\Library\Zeit\Time;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * The ZeitTimeType converts the Zeit model in/from DateTime to be usable with the regular TimeType.
 */
final class ZeitTimeType extends AbstractType implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): ?string
    {
        return TimeType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function transform(mixed $value): mixed
    {
        if ($value instanceof Time) {
            return $value->toDateTime();
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return Time::fromDateTime($value);
        }

        return $value;
    }
}

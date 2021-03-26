<?php

declare(strict_types=1);

namespace IServ\Bridge\Zeit\Form\Type;

use IServ\Library\Zeit\Date;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * The ZeitDateType converts the Zeit model in/from DateTime to be usable with the regular DateType.
 */
final class ZeitDateType extends AbstractType implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return DateType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if ($value instanceof Date) {
            return $value->toDateTime();
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return Date::fromDateTime($value);
        }

        return $value;
    }
}

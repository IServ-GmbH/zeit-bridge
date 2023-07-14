<?php

declare(strict_types=1);

namespace IServ\Bridge\Zeit\Tests\Unit\Form\Type;

use IServ\Bridge\Zeit\Form\Type\ZeitTimeType;
use IServ\Library\Zeit\Time;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @covers \IServ\Bridge\Zeit\Form\Type\ZeitTimeType
 * @uses \IServ\Library\Zeit\Time
 */
final class ZeitTimeTypeTest extends TypeTestCase
{
    public function testType(): void
    {
        $type = new ZeitTimeType();

        $this->assertSame(TimeType::class, $type->getParent());

        $time = new Time('12:37:00');
        $transformed = $type->transform($time);

        $this->assertInstanceOf(\DateTimeImmutable::class, $transformed);
        $this->assertSame('12:37:00', $transformed->format('H:i:s'));

        $dateTime = new \DateTimeImmutable('2021-04-22 12:37:00');
        $reverseTransformed = $type->reverseTransform($dateTime);

        $this->assertInstanceOf(Time::class, $reverseTransformed);
        $this->assertSame('12:37:00', $reverseTransformed->getValue());
    }

    public function testOnlyTimeWillBeTransformed(): void
    {
        $type = new ZeitTimeType();

        $this->assertNull($type->transform(null));
        $this->assertNull($type->reverseTransform(null));

        $this->assertSame('test', $type->transform('test'));
        $this->assertSame('test', $type->reverseTransform('test'));
    }

    public function testSubmit(): void
    {
        $time = new Time('12:37:00');

        $model = new \stdClass();
        $model->zeit = $time;

        $formData = [
            'zeit' => '09:32:05',
        ];

        $form = $this->factory->createBuilder(FormType::class, $model)
            ->add('zeit', ZeitTimeType::class, [
                'widget' => 'single_text',
                'with_seconds' => true,
            ])
            ->getForm()
        ;

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $this->assertInstanceOf(Time::class, $model->zeit);
        $this->assertSame('09:32:05', $model->zeit->getValue());
    }
}

<?php

declare(strict_types=1);

namespace IServ\Bridge\Zeit\Tests\Unit\Form\Type;

use IServ\Bridge\Zeit\Form\Type\ZeitDateType;
use IServ\Library\Zeit\Date;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @covers \IServ\Bridge\Zeit\Form\Type\ZeitDateType
 * @uses \IServ\Library\Zeit\Date
 */
final class ZeitDateTypeTest extends TypeTestCase
{
    public function testType(): void
    {
        $type = new ZeitDateType();

        $this->assertSame(DateType::class, $type->getParent());

        $date = new Date('2021-04-22');
        $transformed = $type->transform($date);

        $this->assertInstanceOf(\DateTimeImmutable::class, $transformed);
        $this->assertSame('2021-04-22', $transformed->format('Y-m-d'));

        $dateTime = new \DateTimeImmutable('2021-04-22 12:34:56');
        $reverseTransformed = $type->reverseTransform($dateTime);

        $this->assertInstanceOf(Date::class, $reverseTransformed);
        $this->assertSame('2021-04-22', $reverseTransformed->getValue());
    }

    public function testOnlyTimeWillBeTransformed(): void
    {
        $type = new ZeitDateType();

        $this->assertNull($type->transform(null));
        $this->assertNull($type->reverseTransform(null));

        $this->assertSame('test', $type->transform('test'));
        $this->assertSame('test', $type->reverseTransform('test'));
    }

    public function testSubmit(): void
    {
        $date = new Date('2021-04-22');

        $model = new \stdClass();
        $model->zeit = $date;

        $formData = [
            'zeit' => '2021-04-29',
        ];

        $form = $this->factory->createBuilder(FormType::class, $model)
            ->add('zeit', ZeitDateType::class, [
                'widget' => 'single_text',
            ])
            ->getForm()
        ;

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $this->assertInstanceOf(Date::class, $model->zeit);
        $this->assertSame('2021-04-29', $model->zeit->getValue());
    }
}

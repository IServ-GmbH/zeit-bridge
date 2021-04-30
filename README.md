# IServ Zeit-Bridge Library

[![coverage report](https://git.iserv.eu/iserv/lib/zeit-bridge/badges/master/coverage.svg)](https://git.iserv.eu/iserv/lib/zeit/commits/master)
[![pipeline status](https://git.iserv.eu/iserv/lib/zeit-bridge/badges/master/pipeline.svg)](https://git.iserv.eu/iserv/lib/zeit/commits/master)

## Basics

The library integrates the Date and Time domain objects from the [Zeit](https://git.iserv.eu/iserv/lib/zeit) library into Doctrine and Symfony.

## Usage

### Date and Time types for Doctrine

You can annotate your entity fields with `zeit_date` or `zeit_time` and Doctrine will convert the corresponding database fields
`DATE` and `TIME` into the domain objects instead of hydrating PHP's native `DateTime` objects.

```php
use Doctrine\ORM\Mapping as ORM;
use IServ\Library\Zeit\Time;

class RestPeriod
{
    /**
     * @ORM\Column(name="`end`", type="zeit_time", nullable=false)
     *
     * @var Time
     */
    private $start;
    
    public function getStart(): Time
    {
        return $this->start;
    }
}
```

Instead of the need to handle `\DateTime` objects holding data you don't need (e.g. the current time or date), you only get the domain data
you want to have.

### Date and Time types for Forms

The library also offers `ZeitDateType` and `ZeitTimeType` for Symfony forms. You can use those types to map your model into a form and use
the underlying date and time form types. The Zeit form types will automatically transform the data to native `DateTime` objects where this
is needed.

```php
use IServ\Bridge\Zeit\Form\Type\ZeitTimeType;
use IServ\Library\Zeit\Time;
use Symfony\Component\Validator\Constraints as Assert;

class RestPeriodData
{
    /**
     * @Assert\NotNull(message="This value is required.")
     *
     * @var Time|null
     */
    private $start;

    public function getStart(): ?Time
    {
        return $this->start;
    }

    /**
     * @return $this
     */
    public function setStart(?Time $start): self
    {
        $this->start = $start;

        return $this;
    }
}

// Somewhere in a controller
$data = new RestPeriodData();

$form = $this->createForm(RestPeriodType::class, $data)
    ->add('start', ZeitTimeType::class, [/* ... */])
    ->getForm()
;
```

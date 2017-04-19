Doctrine DateTimeImmutable Types Bundle
=======================================

> This is a Symfony Bundle providing integration for the standalone package  
[`vasek-purchart/doctrine-date-time-immutable-types`](https://github.com/VasekPurchart/Doctrine-Date-Time-Immutable-Types),  
if you are not using Symfony, follow instructions there.

### Why would I want to use this?

All Doctrine date/time based types are using `DateTime` instances, which are mutable. This can lead to breaking encapsulation and therefore bugs. For two reasons:

1) You accidentally modify a date when you are doing some computation on it:

```php
<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class LogRow
{

	// ...

	/**
	 * @ORM/Column(type="datetime")
	 * @var \DateTime
	 */
	private $createdDate;

	/**
	 * @return \DateTime
	 */
	public function getCreatedDate()
	{
		return $this->createdDate;
	}

}
```

```php
<?php

// created date might be modified
// even if this was not intended by the creator
// (there is no "setter" method for this on the entity)
var_dump($logRow->getCreatedDate()); // 2015-01-01 00:00:00
$logRow->getCreatedDate()->modify('+14 days');
var_dump($logRow->getCreatedDate()); // 2015-01-15 00:00:00
```

2) Or you *do* intentionally try to update it, which fails because Doctrine will not see this:
```php
<?php

$product->getRenewDate()->modify('+1 year');
$entityManager->persist($product);
// no updates will be fired because Doctrine could not detect change
// (objects are compared by identity)
$entityManager->flush();
```

You can prevent this behaviour by returning a new instance (cloning) or using [`DateTimeImmutable`](http://php.net/manual/en/class.datetimeimmutable.php) (which returns a new instance when modified). `DateTimeImmutable` is available since PHP 5.5, but Doctrine has not adopted it yet, because it would introduce a [BC break](https://github.com/doctrine/dbal/issues/1882). Maybe it will be supported in Doctrine 3.0, but until then you might want to use this package.

Configuration
-------------

```yaml
# app/config/config.yml
doctrine_date_time_immutable_types:
    # Choose under which names the types will be registered.
    register: add # One of "add"; "replace"; "add_and_replace"; "none"
```

`register`
  * `add` - add types as new - suffixed with `_immutable` (e.g. `datetime_immutable`)
  * `replace` - replace the original types `date`, `time`, `datetime`, `datetimetz`, i.e. making them immutable
  * `add_and_replace` - combine both previous options (e.g. both `datetime` and `datetime_immutable`)
  * `none` - do not register any types - useful for temporary disabling the registration

Usage
-----

If you are using the `replace` option, you don't need to change any property mappings of your entities.

If you are using the `add` option (default), you only have to suffix your field types with `_immutable`:

```php
<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class LogRow
{

	// ...

	/**
	 * @ORM/Column(type="datetime_immutable")
	 * @var \DateTimeImmutable
	 */
	private $createdDate;

	/**
	 * @return \DateTimeImmutable
	 */
	public function getCreatedDate()
	{
		return $this->createdDate;
	}

}
```

```php
<?php

// created date can no longer be modified from outside
var_dump($logRow->getCreatedDate()); // 2015-01-01 00:00:00
$logRow->getCreatedDate()->modify('+14 days');
var_dump($logRow->getCreatedDate()); // 2015-01-01 00:00:00
```

Installation
------------

Install package [`vasek-purchart/doctrine-date-time-immutable-types-bundle`](https://packagist.org/packages/vasek-purchart/doctrine-date-time-immutable-types-bundle) with [Composer](https://getcomposer.org/):

```
composer require vasek-purchart/doctrine-date-time-immutable-types-bundle
```

Register the bundle in your application kernel:
```php
// app/AppKernel.php
public function registerBundles()
{
	return array(
		// ...
		new VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DoctrineDateTimeImmutableTypesBundle(),
	);
}
```

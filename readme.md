Doctrine DateTimeImmutable Types Bundle
=======================================

> This is a Symfony Bundle providing integration for the standalone package  
[`vasek-purchart/doctrine-date-time-immutable-types`](https://github.com/VasekPurchart/Doctrine-Date-Time-Immutable-Types),  
if you are not using Symfony, follow instructions there.

### Why do I want to use this?

All Doctrine date/time based types are using `DateTime` instances, which are mutable. This can lead very easily to breaking encapsulation and therefore bugs:

```php
<?php

// created date might be modified
// even if this was not intended by the creator
var_dump($logRow->getCreatedDate()); // 2015-01-01 00:00:00
$logRow->getCreatedDate()->modify('+14 days'); // same as: ->getCreatedDate()->add(new \DateInterval('P14D'));
var_dump($logRow->getCreatedDate()); // 2015-01-15 00:00:00
```

You can prevent this behaviour by returning a new instance (cloning) or using [`DateTimeImmutable`](http://php.net/manual/en/class.datetimeimmutable.php) (which returns a new instance when modified). `DateTimeImmutable` is available since PHP 5.5, but Doctrine has not adopted it yet, because it would introduce a [BC break](http://www.doctrine-project.org/jira/browse/DBAL-662). Maybe it will be supported in Doctrine 3.0, but until then you might want to use this package.

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

Configuration
-------------

Add this to your `config.yml`:

```yaml
doctrine_date_time_immutable_types:
    # Choose under which names the types will be registered.
    register:             add # One of "add"; "replace"; "add_and_replace"; "none"
```

`register:`
  * `add` - add types as new - suffixed with `_immutable` (e.g. `created_at_immutable`)
  * `replace` - add types with the same name as original, replacing them (e.g. `created_at`)
  * `add_and_replace` - combines both previous options (e.g. both `created_at` and `created_at_immutable`)
  * `none` - does not register any types - can be useful for temporary disabling the registration

Usage
-----

Just use your entities as you normally would, e.g.
```php
<?php
$logRow->getCreatedDate(); // now immutable!
```
No need to change anything in your mapping files (e.g. `User.orm.yml`) or entity files (e.g. `User.php`).

<?php

declare(strict_types = 1);

namespace VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection;

class DoctrineBundleRequiredException extends \Exception
{

	public function __construct(\Throwable $previous = null)
	{
		parent::__construct('DoctrineBundle must be registered for this bundle to work', 0, $previous);
	}

}

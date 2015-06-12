<?php

namespace VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection;

class DoctrineBundleRequiredException extends \Exception
{

	/**
	 * @param \Exception|null $previous
	 */
	public function __construct(\Exception $previous = null)
	{
		parent::__construct('DoctrineBundle must be registered for this bundle to work', 0, $previous);
	}

}

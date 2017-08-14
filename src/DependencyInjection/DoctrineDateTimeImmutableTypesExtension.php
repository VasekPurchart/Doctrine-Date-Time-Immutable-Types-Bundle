<?php

declare(strict_types = 1);

namespace VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection;

use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\DateTimeTzImmutableType;
use Doctrine\DBAL\Types\TimeImmutableType;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineDateTimeImmutableTypesExtension
	extends \Symfony\Component\HttpKernel\DependencyInjection\Extension
	implements \Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface
{

	const DOCTRINE_BUNDLE_ALIAS = 'doctrine';

	/** @var string[] */
	private static $types = [
		Type::DATE_IMMUTABLE => DateImmutableType::class,
		Type::DATETIME_IMMUTABLE => DateTimeImmutableType::class,
		Type::DATETIMETZ_IMMUTABLE => DateTimeTzImmutableType::class,
		Type::TIME_IMMUTABLE => TimeImmutableType::class,
	];

	public function prepend(ContainerBuilder $container)
	{
		if (!$container->hasExtension(self::DOCTRINE_BUNDLE_ALIAS)) {
			throw new \VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection\DoctrineBundleRequiredException();
		}

		$config = $this->getMergedConfig($container);
		$types = [];

		if (in_array($config[Configuration::PARAMETER_REGISTER], [
			Configuration::REGISTER_REPLACE,
		])) {
			foreach (self::$types as $name => $type) {
				$types[str_replace('_immutable', '', $name)] = $type;
			}
		}

		if (count($types) === 0) {
			return;
		}

		$container->loadFromExtension(self::DOCTRINE_BUNDLE_ALIAS, [
			'dbal' => [
				'types' => $types,
			],
		]);
	}

	/**
	 * @param mixed[][] $configs
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 */
	public function load(array $configs, ContainerBuilder $container)
	{
		// nothing to do
	}

	/**
	 * @param mixed[] $config
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection\Configuration
	 */
	public function getConfiguration(array $config, ContainerBuilder $container): Configuration
	{
		return new Configuration(
			$this->getAlias()
		);
	}

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return mixed[]
	 */
	private function getMergedConfig(ContainerBuilder $container)
	{
		$configs = $container->getExtensionConfig($this->getAlias());
		return $this->processConfiguration($this->getConfiguration([], $container), $configs);
	}

}

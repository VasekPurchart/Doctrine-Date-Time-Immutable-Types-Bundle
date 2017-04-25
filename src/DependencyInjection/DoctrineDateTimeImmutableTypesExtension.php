<?php

namespace VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use VasekPurchart\Doctrine\Type\DateTimeImmutable\DateImmutableType;
use VasekPurchart\Doctrine\Type\DateTimeImmutable\DateTimeImmutableType;
use VasekPurchart\Doctrine\Type\DateTimeImmutable\DateTimeTzImmutableType;
use VasekPurchart\Doctrine\Type\DateTimeImmutable\TimeImmutableType;

class DoctrineDateTimeImmutableTypesExtension
	extends \Symfony\Component\HttpKernel\DependencyInjection\Extension
	implements \Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface
{

	const DOCTRINE_BUNDLE_ALIAS = 'doctrine';

	/** @var string[] */
	private static $types = [
		DateImmutableType::class,
		DateTimeImmutableType::class,
		DateTimeTzImmutableType::class,
		TimeImmutableType::class,
	];

	public function prepend(ContainerBuilder $container)
	{
		if (!$container->hasExtension(self::DOCTRINE_BUNDLE_ALIAS)) {
			throw new \VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection\DoctrineBundleRequiredException();
		}

		$config = $this->getMergedConfig($container);
		$types = [];

		if (in_array($config[Configuration::PARAMETER_REGISTER], [
			Configuration::REGISTER_ADD,
			Configuration::REGISTER_ADD_AND_REPLACE,
		])) {
			foreach (self::$types as $type) {
				$types[$type::NAME] = $type;
			}
		}

		if (in_array($config[Configuration::PARAMETER_REGISTER], [
			Configuration::REGISTER_REPLACE,
			Configuration::REGISTER_ADD_AND_REPLACE,
		])) {
			foreach (self::$types as $type) {
				$types[str_replace('_immutable', '', $type::NAME)] = $type;
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
	public function getConfiguration(array $config, ContainerBuilder $container)
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

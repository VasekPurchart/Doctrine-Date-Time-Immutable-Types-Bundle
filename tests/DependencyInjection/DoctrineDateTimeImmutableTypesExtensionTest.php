<?php

namespace VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection;

use Doctrine\DBAL\Types\Type;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use VasekPurchart\Doctrine\Type\DateTimeImmutable\DateImmutableType;
use VasekPurchart\Doctrine\Type\DateTimeImmutable\DateTimeImmutableType;
use VasekPurchart\Doctrine\Type\DateTimeImmutable\DateTimeTzImmutableType;
use VasekPurchart\Doctrine\Type\DateTimeImmutable\TimeImmutableType;

class DoctrineDateTimeImmutableTypesExtensionTest extends \PHPUnit\Framework\TestCase
{

	/** @var string[][] */
	private static $immutableTypes = [
		DateImmutableType::NAME => DateImmutableType::class,
		DateTimeImmutableType::NAME => DateTimeImmutableType::class,
		DateTimeTzImmutableType::NAME => DateTimeTzImmutableType::class,
		TimeImmutableType::NAME => TimeImmutableType::class,
	];

	/** @var string[][] */
	private static $replaceTypes = [
		Type::DATE => DateImmutableType::class,
		Type::DATETIME => DateTimeImmutableType::class,
		Type::DATETIMETZ => DateTimeTzImmutableType::class,
		Type::TIME => TimeImmutableType::class,
	];

	/**
	 * @return string[][]
	 */
	public function registrationTypesProvider()
	{
		return [
			[Configuration::REGISTER_ADD, self::$immutableTypes],
			[Configuration::REGISTER_REPLACE, self::$replaceTypes],
			[Configuration::REGISTER_ADD_AND_REPLACE, array_merge(self::$immutableTypes, self::$replaceTypes)],
			[Configuration::REGISTER_NONE, []],
		];
	}

	public function testDependsOnDoctrineBundle()
	{
		$containerBuilder = new ContainerBuilder();
		$extension = new DoctrineDateTimeImmutableTypesExtension();

		$this->expectException(\VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection\DoctrineBundleRequiredException::class);
		$extension->prepend($containerBuilder);
	}

	public function testDefaultRegisterImmutable()
	{
		$types = $this->getDoctrineTypesConfig([]);
		$this->assertTypes(self::$immutableTypes, $types);
	}

	/**
	 * @dataProvider registrationTypesProvider
	 *
	 * @param string $register
	 * @param string[][] $expectedTypes
	 */
	public function testRegisterByConfig($register, $expectedTypes)
	{
		$types = $this->getDoctrineTypesConfig([
			Configuration::PARAMETER_REGISTER => $register,
		]);
		$this->assertTypes($expectedTypes, $types);
	}

	/**
	 * @param mixed[] $extensionConfig
	 * @return \Doctrine\DBAL\Types\Type[] format: type name (string) => type class)
	 */
	private function getDoctrineTypesConfig(array $extensionConfig)
	{
		$doctrineExtension = new DoctrineExtension();
		$extension = new DoctrineDateTimeImmutableTypesExtension();

		$containerBuilder = new ContainerBuilder();
		$containerBuilder->registerExtension($doctrineExtension);
		$containerBuilder->registerExtension($extension);
		$containerBuilder->loadFromExtension($extension->getAlias(), $extensionConfig);

		$extension->prepend($containerBuilder);

		$doctrineConfig = $containerBuilder->getExtensionConfig($doctrineExtension->getAlias());

		if (!isset($doctrineConfig[0]) || !isset($doctrineConfig[0]['dbal']) || !isset($doctrineConfig[0]['dbal']['types'])) {
			return [];
		}

		return $containerBuilder->getExtensionConfig($doctrineExtension->getAlias())[0]['dbal']['types'];
	}

	/**
	 * @param \Doctrine\DBAL\Types\Type[] $expectedTypes format: type name (string) => type class)
	 * @param \Doctrine\DBAL\Types\Type[] $actualTypes format: type name (string) => type class)
	 */
	private function assertTypes(array $expectedTypes, array $actualTypes)
	{
		foreach ($expectedTypes as $typeName => $typeClass) {
			$this->assertArraySubset([$typeName => $typeClass], $actualTypes);
		}
		$this->assertCount(count($expectedTypes), $actualTypes);
	}

	public function testLoadHasNoEffect()
	{
		$containerBuilder = new ContainerBuilder();
		$extension = new DoctrineDateTimeImmutableTypesExtension();

		$containerBuilderOriginal = serialize($containerBuilder);

		$extension->load([], $containerBuilder);

		$this->assertEquals($containerBuilderOriginal, serialize($containerBuilder));
	}

}

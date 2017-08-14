<?php

declare(strict_types = 1);

namespace VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\DateTimeTzImmutableType;
use Doctrine\DBAL\Types\TimeImmutableType;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineDateTimeImmutableTypesExtensionTest extends \PHPUnit\Framework\TestCase
{

	/** @var string[] */
	private static $replaceTypes = [
		Type::DATE => DateImmutableType::class,
		Type::DATETIME => DateTimeImmutableType::class,
		Type::DATETIMETZ => DateTimeTzImmutableType::class,
		Type::TIME => TimeImmutableType::class,
	];

	/**
	 * @return mixed[]
	 */
	public function registrationTypesProvider(): array
	{
		return [
			[Configuration::REGISTER_ADD, []],
			[Configuration::REGISTER_REPLACE, self::$replaceTypes],
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
		$this->assertTypes([], $types);
	}

	/**
	 * @dataProvider registrationTypesProvider
	 *
	 * @param string $register
	 * @param string[] $expectedTypes
	 */
	public function testRegisterByConfig(string $register, array $expectedTypes)
	{
		$types = $this->getDoctrineTypesConfig([
			Configuration::PARAMETER_REGISTER => $register,
		]);
		$this->assertTypes($expectedTypes, $types);
	}

	/**
	 * @param mixed[] $extensionConfig
	 * @return string[] format: type name (string) => type class)
	 */
	private function getDoctrineTypesConfig(array $extensionConfig): array
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
	 * @param string[] $expectedTypes format: type name (string) => type class)
	 * @param string[] $actualTypes format: type name (string) => type class)
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

<?php

declare(strict_types = 1);

namespace VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements \Symfony\Component\Config\Definition\ConfigurationInterface
{

	const PARAMETER_REGISTER = 'register';

	const REGISTER_ADD = 'add';
	const REGISTER_REPLACE = 'replace';

	/** @var string */
	private $rootNode;

	public function __construct(string $rootNode)
	{
		$this->rootNode = $rootNode;
	}

	public function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root($this->rootNode);

		$rootNode
			->children()
				->enumNode(self::PARAMETER_REGISTER)
					->info('Choose under which names the types will be registered.')
					->values([
						self::REGISTER_ADD,
						self::REGISTER_REPLACE,
					])
					->defaultValue(self::REGISTER_ADD)
					->end()
				->end()
			->end();

		return $treeBuilder;
	}

}

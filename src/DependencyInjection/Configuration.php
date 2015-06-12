<?php

namespace VasekPurchart\DoctrineDateTimeImmutableTypesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements \Symfony\Component\Config\Definition\ConfigurationInterface
{

	const PARAMETER_REGISTER = 'register';

	const REGISTER_ADD = 'add';
	const REGISTER_ADD_AND_REPLACE = 'add_and_replace';
	const REGISTER_NONE = 'none';
	const REGISTER_REPLACE = 'replace';

	/** @var string */
	private $rootNode;

	/**
	 * @param string $rootNode
	 */
	public function __construct($rootNode)
	{
		$this->rootNode = $rootNode;
	}

	/**
	 * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
	 */
	public function getConfigTreeBuilder()
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
						self::REGISTER_ADD_AND_REPLACE,
						self::REGISTER_NONE,
					])
					->defaultValue(self::REGISTER_ADD)
					->end()
				->end()
			->end();

		return $treeBuilder;
	}

}

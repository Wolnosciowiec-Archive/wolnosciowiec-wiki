<?php declare(strict_types=1);

namespace WikiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Tells the framework that we need to register a group
     * of configuration, that is required for this bundle to work
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wiki');

        $rootNode
            ->children()
            ->arrayNode('repositories')
                ->prototype('array')
                    ->children()
                        ->scalarNode('address')->end()
                        ->scalarNode('branch')->end()
                        ->scalarNode('fetcher')->end()
                        ->scalarNode('public')->end()
                        ->scalarNode('index_path')->isRequired(false)->end()
                        ->arrayNode('domains')
                            ->prototype('scalar')
                        ->isRequired(false)
                    ->end()
                ->isRequired()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
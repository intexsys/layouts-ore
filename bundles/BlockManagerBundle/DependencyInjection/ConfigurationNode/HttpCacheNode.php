<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class HttpCacheNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('http_cache');
        $node = $treeBuilder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('invalidation')
                    ->canBeDisabled()
                    ->addDefaultsIfNotSet()
                ->end()
            ->end();

        return $node;
    }
}

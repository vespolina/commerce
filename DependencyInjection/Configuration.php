<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Richard Shank <develop@zestic.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vespolina_product');
        $rootNode
            ->children()
                ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
            ->end();
        
        $this->addProductManagerSection($rootNode);
        $this->addProductSection($rootNode);

        return $treeBuilder;
    }

    private function addProductManagerSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
        
                ->arrayNode('product_manager')
                    ->children()
                    ->scalarNode('primary_identifier')->isRequired()->cannotBeEmpty()->end()
        
                    ->arrayNode('identifiers')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
        
            ->end()
        ;
    }

    private function addProductSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()

                ->arrayNode('product')
                    ->arrayNode('form')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('type')->defaultValue('vespolina.product.form.type')->end()
                            ->scalarNode('handler')->defaultValue('vespolina.product.form.handler')->end()
                            ->scalarNode('name')->defaultValue('vespolina_product_form')->cannotBeEmpty()->end()
                            ->scalarNode('data_class')->defaultValue('vespolina.product.form.model.check_product.class')->end()
                        ->end()
                    ->end()
                ->end()
        
            ->end()
        ;
    }
}

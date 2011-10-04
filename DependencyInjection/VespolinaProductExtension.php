<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

use Vespolina\ProductBundle\DependencyInjection\Configuration;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class VespolinaProductExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (!in_array(strtolower($config['db_driver']), array('mongodb'))) {
            throw new \InvalidArgumentException(sprintf('Invalid db driver "%s".', $config['db_driver']));
        }
        $loader->load(sprintf('%s.xml', $config['db_driver']));
        $loader->load('product.xml');

        if (isset($config['product_manager'])) {
            $this->configureProductManager($config['product_manager'], $container);
        }
    }

    protected function configureProductManager(array $config, ContainerBuilder $container)
    {
        if (isset($config['identifiers'])) {
            $container->setParameter('vespolina_product.product_manager.identifiers', $config['identifiers']);
        }
        
        if (isset($config['primary_identifier'])) {
            $container->setParameter('vespolina_product.product_manager.primary_identifier', $config['primary_identifier']);
        }
    }

    protected function configureProduct(array $config, ContainerBuilder $container)
    {
        if (isset($config['form'])) {
            if (isset($config['type'])) {
                $container->setParameter('vespolina.product.form.type', $config['form']['type']);
            }
            if (isset($config['handler'])) {
                $container->setParameter('vespolina.product.form.handler', $config['form']['handler']);
            }
            if (isset($config['name'])) {
                $container->setParameter('vespolina_product_form', $config['form']['name']);
            }
            if (isset($config['data_class'])) {
                $container->setParameter('vespolina.product.form.model.check_product.class', $config['form']['data_class']);
            }
        }
    }
}

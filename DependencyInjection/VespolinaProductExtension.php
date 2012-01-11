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
        $loader->load('identifiers.xml');
        $loader->load('features.xml');
        $loader->load('options.xml');
        $loader->load('product.xml');

        if (isset($config['identifier_set'])) {
            $this->configureIdentifierSet($config['identifier_set'], $container);
        }
        if (isset($config['feature'])) {
            $this->configureFeature($config['feature'], $container);
        }
        if (isset($config['option_group'])) {
            $this->configureOptionGroup($config['option_group'], $container);
        }
        if (isset($config['configured_option_group'])) {
            $this->configureConfiguredOptionGroup($config['configured_option_group'], $container);
        }
        if (isset($config['option'])) {
            $this->configureOption($config['option'], $container);
        }
        if (isset($config['product_manager'])) {
            $this->configureProductManager($config['product_manager'], $container);
        }
        if (isset($config['product'])) {
            $this->configureProduct($config['product'], $container);
        }
    }

    protected function configureIdentifierSet(array $config, ContainerBuilder $container)
    {
        if (isset($config['form'])) {
            $formConfig = $config['form'];
            if (isset($formConfig['type'])) {
                $container->setParameter('vespolina.identifier_set.form.type.class', $formConfig['type']);
            }
            if (isset($formConfig['name'])) {
                $container->setParameter('vespolina_identifier_set', $formConfig['name']);
            }
            if (isset($formConfig['data_class'])) {
                $container->setParameter('vespolina.identifier_set.form.model.data_class.class', $formConfig['data_class']);
            }
        }
    }

    protected function configureFeature(array $config, ContainerBuilder $container)
    {
        if (isset($config['form'])) {
            $formConfig = $config['form'];
            if (isset($formConfig['type'])) {
                $container->setParameter('vespolina.feature.form.type.class', $formConfig['type']);
            }
            if (isset($formConfig['name'])) {
                $container->setParameter('vespolina_feature', $formConfig['name']);
            }
            if (isset($formConfig['data_class'])) {
                $container->setParameter('vespolina.feature.form.model.data_class.class', $formConfig['data_class']);
            }
        }
    }

    protected function configureOptionGroup(array $config, ContainerBuilder $container)
    {
        if (isset($config['form'])) {
            $formConfig = $config['form'];
            if (isset($formConfig['type'])) {
                $container->setParameter('vespolina.option_group.form.type.class', $formConfig['type']);
            }
            if (isset($formConfig['name'])) {
                $container->setParameter('vespolina_option_group', $formConfig['name']);
            }
            if (isset($formConfig['data_class'])) {
                $container->setParameter('vespolina.option_group.form.model.data_class.class', $formConfig['data_class']);
            }
        }
    }

    protected function configureConfiguredOptionGroup(array $config, ContainerBuilder $container)
    {
        if (isset($config['class'])) {
            $container->setParameter('vespolina.product.model.configured_option_group.class', $config['class']);
        }
        if (isset($config['form'])) {
            $formConfig = $config['form'];
            if (isset($formConfig['data_class'])) {
                $container->setParameter('vespolina.configured_option_group.form.model.data_class.class', $formConfig['data_class']);
            }
        }
    }

    protected function configureOption(array $config, ContainerBuilder $container)
    {
        if (isset($config['form'])) {
            $formConfig = $config['form'];
            if (isset($formConfig['type'])) {
                $container->setParameter('vespolina.option.form.type.class', $formConfig['type']);
            }
            if (isset($formConfig['name'])) {
                $container->setParameter('vespolina_option', $formConfig['name']);
            }
            if (isset($formConfig['data_class'])) {
                $container->setParameter('vespolina.option.form.model.data_class.class', $formConfig['data_class']);
            }
        }
    }

    protected function configureProductManager(array $config, ContainerBuilder $container)
    {
        if (isset($config['identifiers'])) {
            $container->setParameter('vespolina.product.product_manager.identifiers', $config['identifiers']);
        }

        if (isset($config['image_manager'])) {
            $container->setAlias('vespolina.product.image_manager', $config['image_manager']);
        }
    }

    protected function configureProduct(array $config, ContainerBuilder $container)
    {
        if (isset($config['class'])) {
            $container->setParameter('vespolina.product.model.product.class', $config['class']);
        }
        if (isset($config['form'])) {
            $formConfig = $config['form'];
            if (isset($formConfig['type'])) {
                $container->setParameter('vespolina.product.form.type.class', $formConfig['type']);
            }
            if (isset($formConfig['handler_class'])) {
                $container->setParameter('vespolina.product.form.handler.class', $formConfig['handler_class']);
            }
            if (isset($formConfig['handler_service'])) {
                $container->setAlias('vespolina.product.form.handler', $formConfig['handler_service']);
            }
            if (isset($formConfig['name'])) {
                $container->setParameter('vespolina_product_form', $formConfig['name']);
            }
            if (isset($formConfig['data_class'])) {
                $container->setParameter('vespolina.product.form.model.check_product.class', $formConfig['data_class']);
            }
        }
    }
}

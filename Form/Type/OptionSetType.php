<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\FormBuilder;
use Vespolina\ProductBundle\Form\Type\OptionGroupType;

class OptionSetType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $listener = new ResizeFormListener(
            $builder->getFormFactory(),
            $options['type'],
            $options['options'],
            $options['allow_add'],
            $options['allow_delete']
        );

        $builder
            ->add('option_groups', 'collection', array(
                'type' => new OptionGroupType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ->add('identifierSet', new IdentifierSetType())


            ->addEventSubscriber($listener)
            ->setAttribute('allow_add', $options['allow_add'])
            ->setAttribute('allow_delete', $options['allow_delete'])
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'allow_add'     => false,
            'allow_delete'  => false,
            'data_class'    => 'Vespolina\ProductBundle\Document\OptionSet',
            'prototype'     => true,
            'type'          => 'text',
            'options'       => array(),
        );
    }

    function getName()
    {
        return 'vespolina_product_option_set';
    }
}

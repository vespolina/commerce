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
        $builder
            ->add('option_groups', 'collection', array(
                'type'           => new OptionGroupType(),
                'allow_add'      => true,
                'allow_delete'   => true,
                'required'       => false,
                'by_reference'   => false,
                'prototype_name' => 'group',
            ))
            ->add('identifierSet', new IdentifierSetType())
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Vespolina\ProductBundle\Document\OptionSet',
        );
    }

    function getName()
    {
        return 'vespolina_product_option_set';
    }
}

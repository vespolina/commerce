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

class OptionSetType extends AbstractType
{
    protected $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('option_groups', 'collection', array(
                'type'           => 'vespolina_option_group',
                'allow_add'      => true,
                'allow_delete'   => true,
                'required'       => false,
                'by_reference'   => false,
                'prototype_name' => 'group',
            ))
            ->add('identifier_set', new IdentifierSetType())
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => $this->dataClass,
        );
    }

    function getName()
    {
        return 'vespolina_product_option_set';
    }
}

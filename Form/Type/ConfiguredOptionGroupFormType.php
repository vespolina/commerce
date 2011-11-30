<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ConfiguredOptionGroupFormType extends AbstractType
{
    protected $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'required' => false,
            ))
            ->add('display', 'text', array(
                'required' => false,
            ))
            ->add('options', 'collection', array(
                'type'           => 'vespolina_configured_option',
                'allow_add'      => true,
                'allow_delete'   => true,
                'required'       => false,
                'prototype_name' => 'option',
                'by_reference'   => false,
            ))
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
        return 'vespolina_product_configured_option_group';
    }
}

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
use Vespolina\ProductBundle\Form\Type\ConfiguredOptionGroupFormType;

class OptionGroupFormType extends ConfiguredOptionGroupFormType
{
    public function __construct($dataClass)
    {
        parent::__construct($dataClass);
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('required', 'checkbox', array(
                'value'    => true,
                'required' => false,
            ))
        ;
    }

    function getParent()
    {
        return 'configured_vespolina_product_option_group';
    }

    function getName()
    {
        return 'vespolina_product_option_group';
    }
}

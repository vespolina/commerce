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
use Vespolina\ProductBundle\Form\Type\OptionType;

class OptionGroupType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'required' => false,
            ))
            ->add('options', 'collection', array(
                'type' => new OptionType(),
                'allow_add' => true,
                'required' => false,
                'by_reference' => false,
            ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Vespolina\ProductBundle\Document\OptionGroup',
        );
    }

    function getName()
    {
        return 'vespolina_product_option_group';
    }
}

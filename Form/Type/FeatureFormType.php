<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FeatureFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('type', 'text', array(
                'required' => false,
            ))
            ->add('name', 'text', array(
                'required' => false,
            ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Vespolina\ProductBundle\Document\Feature',
        );
    }

    function getName()
    {
        return 'vespolina_product_feature';
    }
}

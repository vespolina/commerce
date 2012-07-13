<?php

namespace Vespolina\CartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class Product extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'Vespolina\ProductBundle\Model\Product',
            'cascade_validation' => true,
        );
    }

    public function getName()
    {
        return 'product';
    }

}

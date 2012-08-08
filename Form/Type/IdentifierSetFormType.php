<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vespolina\ProductBundle\Form\Type\IdentifierFormType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IdentifierSetFormType extends AbstractType
{
    protected $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifiers', 'collection', array(
                'type' => new IdentifierFormType(),
                'allow_add' => true,
                'by_reference' => false,
            ))
        ;
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
        ));
    }

    function getName()
    {
        return 'vespolina_product_identifier_set';
    }
}

<?php
/**
* (c) 2011 Vespolina Project http://www.vespolina-project.org
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/
namespace Vespolina\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Vespolina\ProductBundle\Form\Type\FeatureType;
use Vespolina\ProductBundle\Form\Type\IdentifierFormType;

/**
 * @author Richard Shank <develop@zestic.com>
 */
class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('primaryIdentifier', new IdentifierSetType())
            ->add('options', 'collection', array(
                'required' => false,
                'type' => new OptionSetType(),
                'allow_add' => true,
                'by_reference' => false,
            ))
            ->add('features', 'collection', array(
                'required' => false,
                'type' => new FeatureType(),
                'allow_add' => true,
                'by_reference' => false,
            ))
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName()
    {
        return 'vespolina_product';
    }
}

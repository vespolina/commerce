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

class UPCIdentifierFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('code')
        ;
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'Vespolina\ProductBundle\Document\UPCIdentifier',
        );
    }

    function getName()
    {
        return 'vespolina_product_upc_identifier';
    }
}

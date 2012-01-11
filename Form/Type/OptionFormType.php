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

class OptionFormType extends AbstractType
{
    protected $dataClass;
    protected $name;

    public function __construct($dataClass, $name)
    {
        $this->dataClass = $dataClass;
        $this->name = $name;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('display', 'text', array(
                'required' => false,
            ))
            ->add('value', 'text', array(
                'required' => false,
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
        return $this->name;
    }
}

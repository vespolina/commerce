<?php
/**
* (c) 2011 Vespolina Project http://www.vespolina-project.org
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/
namespace Vespolina\ProductBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Vespolina\ProductBundle\Form\Model\CheckProduct;
use Vespolina\ProductBundle\Model\ProductAdminManagerInterface;
/**
 * @author Richard Shank <develop@zestic.com>
 */
class OptionGroupFormHandler
{
    protected $request;
    protected $productAdminManager;
    protected $form;

    public function __construct(Form $form, Request $request, ProductAdminManagerInterface $productAdminManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->productAdminManager = $productAdminManager;
    }

    public function process($optionGroup)
    {
        $this->form->setData($optionGroup);

        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $this->productAdminManager->update($optionGroup);

                return true;
            }
        }
        return false;
    }
}

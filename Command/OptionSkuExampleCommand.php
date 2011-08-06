<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class OptionSkuExampleCommand extends ContainerAwareCommand
{
    protected $productManager;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('vespolina:product:option-sku-example')
            ->setDescription('Create a sample data set this specific skus for different options.')
            ->setHelp(<<<EOT
This command builds the data for the example in this conversation

https://groups.google.com/forum/#!topic/vespolina-dev/a7zTBhSJ0Yc
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->productManager = $this->getContainer()->get('vespolina.product_manager');
        
        $product = $this->productManager->createProduct();
        $product->setName('Vespolina T-Shirt');
        $product->setDescription('This stylish t-shirt sports the not yet designed Vespolina Project logo');

        $options['color']['RD'] = $this->productManager->createOption('color', 'red');
        $options['color']['BL'] = $this->productManager->createOption('color', 'blue');
        $options['color']['GN'] = $this->productManager->createOption('color', 'green');
        $options['size']['01'] = $this->productManager->createOption('size', 'small');
        $options['size']['02'] = $this->productManager->createOption('size', 'medium');
        $options['size']['03'] = $this->productManager->createOption('size', 'large');

        $primaryIdentifier = $this->productManager->getPrimaryIdentifier();
        foreach ($options['size'] as $sizeCode => $sizeOption) {
            foreach ($options['color'] as $colorCode => $colorOption) {
                $identifier = new $primaryIdentifier;
                $identifier->setCode('MYSHRT' . $sizeCode . $colorCode);
                $identifierSet = $this->productManager->createIdentifierSet($identifier);
                $identifierSet->addOption($sizeOption);
                $identifierSet->addOption($colorOption);
                $this->productManager->addIdentifierSetToProduct($identifierSet, $product);
            }
        }

        $this->productManager->updateProduct($product);

        $output->writeln('Example t-shirt created');
    }
}

Vespolina ProductBundle

General Concepts
================

The Product class is a container for basic product information. This includes product features, product options and
identifiers.

The minimal data needed for a Product is a name.

Identifiers
-----------

Identifiers are any type of system used to identify a product or variations of the product. Examples of identifiers are
SKUs, ISBN, UPC, EAN or ASIN. It is possible for a single product to have more than one identifier assigned to it. For
example, Test Driven Development by Kent Beck, has the ISBN-10 0321146530, ISBN-13 978-0321146533 and ASIN 0785342146530.
These 3 identifiers would be part of the product's primary IdentifierSet.

Features
--------

Features are attributes of a product, using the same example, the following are features of Kent Beck's Test Driven
Development

   Features
   
   +-----------+-----------------------------+
   |  type     | name                        |
   +-----------+-----------------------------+
   | Binding   | Paperback                   |
   | Pages     | 240                         |
   | Publisher | Addison-Wesley Professional |
   | Language  | English                     |
   +-----------+-----------------------------+

   

Using the VespolinaProductBundle
================================

Extending a Product
-------------------

The simplest way to extend the ProductBundle is to use SonataEasyExtendsBundle. When you have it installed then
the following command will build out the Documents for you.

    ``app/console sonata:easy-extends:generate -d src VespolinaProductBundle``

NOTE until the configuration is updated, you cannot set up a manual product

You can build the class manually also

    # Application\MyBundle\Document\MyProduct.php
    
    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Vespolina\ProductBundle\Document\BaseProduct;

    class MyProduct extends BaseProduct
    {
        /**
         * @MongoDb/Id(strategy="auto")
         */
        protected $id;

        public function getId()
        {
            return $this->id;
        }
    }


Extending
=========

Lets say you need to store the vendor with the product. There are a few steps you would need to take. First, in your
product class add the property and getter and setter.

    # Application\Vespolina\ProductBundle\Document\Product.php

    class Product extends BaseProduct
    {
        ...

        protected $vendor;

        public function getVendor()
        {
            return $this->vendor;
        }

        public function setVendor($vendor)
        {
            $this->vendor = $vendor;
        }
    }

Next, you'll need to add the mapping for doctrine.

    # Application\Vespolina\ProductBundle\Resources\config\doctrine\product.mongodb.xml

    ...

        <document name="Application\Vespolina\ProductBundle\Document\Product" collection="vespolinaProduct">

            ...

            <field name="vendor" fieldName="vendor" type="string" />

            ...

        </document

Adding to the form

If you want to override the Product form, create a new FormType class

    class MyProductFormType extends Vespolina\ProductBundle\Form\Type\ProductFormType
    {
        public function buildForm(FormBuilder $builder, array $options)
        {
            parent::buildForm($builder, $options);

            $builder->add('custom');
        }

    }

In the configuration you would set the the form type to your custom form

    vespolina_product:
        db_driver: mongodb
        product:
            form:
                type: My\Namespace\MyProductFormType

Now Vespolina will use your product type in the forms.


Configuration reference
=======================

All available configuration options are listed below with their default values:

    # app/config/vespolina.yml
    vespolina_product:
        db_driver:      ~ # Required
        product_manager:
            primary_identifier: ~ # Required
            identifiers: id
                id: Vespolina\ProductBundle\Model\Identifier\IdIdentifier
        product:
            class: Application\Vespolina\ProductBundle\Document\Product
            form:
                type:               vespolina.product.form.type
                handler_service:    vespolina.product.form.handler
                name:               vespolina_product_form


Product Types
-------------

NOTE: THIS WILL PROBABLY CHANGE

These are valid types of products
* Product::PHYSICAL
* Product::UNIQUE
* Product::DOWNLOAD
* Product::TIME
* Product::SERVICE

*Product::PHYSICAL*

*Product::UNIQUE*

*Product::DOWNLOAD*

*Product::TIME*

*Product::SERVICE*

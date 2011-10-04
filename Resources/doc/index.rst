Vespolina ProductBundle

General Concepts
================

The Product class is a container for basic product information. This includes product features, product options and
identifiers, such as SKUs, UPC, EAN or ASIN.

The minimal data needed for a Product is a name and product type.

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

Extending a Product
===================

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

If you want to override the form, create a new FormType class

TODO


Configuration reference
=======================

All available configuration options are listed below with their default values::

    # app/config/vespolina.yml
    vespolina_product:
        db_driver:      ~ # Required
        product_manager:
            primary_identifier: ~ # Required
            identifiers: id
                id: Vespolina\ProductBundle\Model\Identifier\IdIdentifier
        product:
            form:
                type:               vespolina.product.form.type
                handler:            vespolina.product.form.handler
                name:               vespolina_product_form

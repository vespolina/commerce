Vespolina ProductBundle

General Concepts
================

The Product class is a container for basic product information. This includes product features, product options and
identifiers, such as SKUs, UPC, EAN or ASIN.

The minimal data needed for a Product is a name and product type.

Product Types
-------------

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

::

    # Application\MyBundle\Document\MyProduct.php
    
    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Vespolina\ProductBundle\Document\BaseProduct;

    class MyProduct extends BaseProduct
    {
        /**
         * @MongoDb/Id(strategy="auto")
         */
        protected $id;

        /**
         * @MongoDb/ReferenceOne(document="Vespolina\ProductBundle\Document\ProductOptions")
         */
        protected $options;

    }   

TODO: configuring product


Configuration reference
=======================

All available configuration options are listed below with their default values::

    # app/config/vespolina.yml
    vespolina_product:
        db_driver:      ~ # Required

<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Manager;

use Doctrine\ORM\QueryBuilder;
use Vespolina\Entity\Pricing\PricingContextInterface;
use Vespolina\Entity\Order\Item;
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\Exception\InvalidConfigurationException;
use Gateway\Query;
use Molino\BaseQuery;
use Vespolina\Entity\Order\OrderEvents;
use Vespolina\Order\Gateway\OrderGatewayInterface;
use Vespolina\Order\Gateway\ItemGatewayInterface;
use Vespolina\Order\Manager\OrderManagerInterface;
use Vespolina\Entity\Pricing\PricingSetInterface;
use Vespolina\Entity\Order\Order;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\NullDispatcher;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
class OrderManager implements OrderManagerInterface
{
    protected $cartClass;
    protected $eventDispatcher;
    protected $eventsClass;
    protected $gateway;
    protected $itemGateway;
    protected $itemClass;
    protected $orderClass;

    function __construct(OrderGatewayInterface $gateway, ItemGatewayInterface $itemGateway, array $classMapping, EventDispatcherInterface $eventDispatcher = null)
    {
        $missingClasses = array();
        foreach (array('cart', 'events', 'item', 'order') as $class) {
            $class = $class . 'Class';
            if (isset($classMapping[$class])) {

                if (!class_exists($classMapping[$class]))
                    throw new InvalidConfigurationException(sprintf("Class '%s' not found as '%s'", $classMapping[$class], $class));

                $this->{$class} = $classMapping[$class];
                continue;
            }
            $missingClasses[] = $class;
        }

        if (count($missingClasses)) {
            throw new InvalidConfigurationException(sprintf("The following partner classes are missing from configuration: %s", join(', ', $missingClasses)));
        }

        if (!$eventDispatcher) {
            $eventDispatcher = new NullDispatcher();
        }

        $this->eventDispatcher = $eventDispatcher;
        $this->gateway = $gateway;
        $this->itemGateway = $itemGateway;
    }

    /**
     * @inheritdoc
     */
    public function addProductToOrder(OrderInterface $order, ProductInterface $product, array $options = null, $quantity = null, $combine = true)
    {
        $quantity = $quantity === null ? 1 : $quantity;

        $item = $this->doAddProductToOrder($order, $product, $options, $quantity, $combine);
        $order->addItem($item);

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function createCart($name = 'default')
    {
        $cart = new $this->cartClass();
        $cart->setName($name);
        $this->initOrder($cart);
        $this->gateway->persistOrder($cart);

        return $cart;
    }

    /**
     * @inheritdoc
     */
    public function createOrder($name = 'default')
    {
        $order = new $this->orderClass();
        $order->setName($name);
        $this->initOrder($order);
        $this->gateway->persistOrder($order);

        return $order;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->doFindBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function findOrdersBy(Query $query)
    {
        return $this->gateway->findOrders($query);
    }

    /**
     * @inheritdoc
     */
    public function findOrderById($id)
    {
        $query = $this->gateway
            ->createQuery('Select')
            ->filterEqual('id', $id)
        ;

        return $this->gateway->findOrder($query);
    }

    /**
     * @inheritdoc
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function findProductInOrder(OrderInterface $cart, ProductInterface $product, array $options = null)
    {
        if ($items = $cart->getItems()) {
            foreach ($cart->getItems() as $item) {
                if ($item->getProduct() == $product) {
                    if ($this->doOptionsMatch($item->getOptions(), $options)) {
                        return $item;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setOrderPricingSet(OrderInterface $cart, PricingSetInterface $pricingSet)
    {
        $rp = new \ReflectionProperty($cart, 'pricingSet');
        $rp->setAccessible(true);
        $rp->setValue($cart, $pricingSet);
        $rp->setAccessible(false);
    }

    /**
     * @inheritdoc
     */
    public function removeProductFromOrder(OrderInterface $cart, ProductInterface $product, array $options = null, $flush = true)
    {
        if (!$options) {
            $options = array();
        }
        $this->doRemoveItemFromOrder($cart, $product, $options);
    }

    /**
     * @inheritdoc
     */
    public function setOrderItemState(ItemInterface $cartItem, $state)
    {
        $rp = new \ReflectionProperty($cartItem, 'state');
        $rp->setAccessible(true);
        $rp->setValue($cartItem, $state);
        $rp->setAccessible(false);

        $orderEvents = $this->eventsClass;
        $this->eventDispatcher->dispatch($orderEvents::UPDATE_ITEM_STATE, $this->eventDispatcher->createEvent($cartItem));
    }

    /**
     * @inheritdoc
     */
    public function setOrderState(OrderInterface $cart, $state)
    {
        $rp = new \ReflectionProperty($cart, 'state');
        $rp->setAccessible(true);
        $rp->setValue($cart, $state);
        $rp->setAccessible(false);

        $eventsClass = $this->eventsClass;
        $this->eventDispatcher->dispatch($eventsClass::UPDATE_ORDER_STATE, $this->eventDispatcher->createEvent($cart));
    }

    public function setItemQuantity(ItemInterface $item, $quantity)
    {
        // todo: trigger events
        $rm = new \ReflectionMethod($item, 'setQuantity');
        $rm->setAccessible(true);
        $rm->invokeArgs($item, array($quantity));
        $rm->setAccessible(false);

        $eventsClass = $this->eventsClass;
        $this->eventDispatcher->dispatch($eventsClass::UPDATE_ITEM, $this->eventDispatcher->createEvent($item));
    }

    /**
     * @inheritdoc
     */
    public function setProductQuantity(OrderInterface $cart, ProductInterface $product, array $options, $quantity)
    {
        $item = $this->findProductInOrder($cart, $product, $options);
        $this->setItemQuantity($item, $quantity);
    }

    /**
     * @inheritdoc
     */
    public function updateOrder(OrderInterface $order)
    {
        $orderEvents = $this->eventsClass;
        $this->eventDispatcher->dispatch($orderEvents::UPDATE_ORDER, $this->eventDispatcher->createEvent($order));
        $this->gateway->updateOrder($order);

        foreach ($order->getItems() as $item) {
            $this->itemGateway->updateItem($item);
        }
    }

    public function updateItem(ItemInterface $item)
    {
        $this->itemGateway->updateItem($item);
    }

    public function updateOrderPricing(OrderInterface $order, PricingContextInterface $context = null)
    {
        $orderEvents = $this->eventsClass;
        $this->eventDispatcher->dispatch(
            $orderEvents::UPDATE_ORDER_PRICE,
            $this->eventDispatcher->createEvent(array($order, $context))
        );
    }

    protected function createItem(ProductInterface $product, array $options = null)
    {
        $className = $this->itemClass;
        $item = new $className();

        $rm = new \ReflectionMethod($item, 'setProduct');
        $rm->setAccessible(true);
        $rm->invokeArgs($item, array($product));
        $rm->setAccessible(false);

        if ($options) {
            $rm = new \ReflectionMethod($item, 'setOptions');
            $rm->setAccessible(true);
            $rm->invokeArgs($item, array($options));
            $rm->setAccessible(false);
        }
        $this->initOrderItem($item);

        return $item;
    }

    /**
     * @inheritdoc
     */
    protected function doFindBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        /** @var \Molino\Doctrine\ORM\SelectQuery $query  */
        $query = $this->gateway->createQuery('Select');

        foreach($criteria as $field => $value) {
            $query->field($field)->equals($value);
        }
        if ($orderBy) {
            $query->orderBy($orderBy);
        }
        if ($limit) {
            $query->limit($limit);
        }
        if ($offset) {
            $query->offset($offset);
        }

        return $this->gateway->findOrders($query);
    }

    protected function doFindOrderById($id)
    {
    }

    protected function doOptionsMatch($itemOptions, $targetOptions)
    {
        if (empty($targetOptions)) {
            if (empty($itemOptions)) {
                return true;
            } else {
                return false;
            }
        }
        if (empty($itemOptions)) {
            return false;
        }
        foreach ($targetOptions as $option) {
            if (!in_array($option, $itemOptions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doUpdateOrder(OrderInterface $order, $andPersist = true)
    {
        $this->gateway->updateOrder($order);
    }

    protected function initOrder(OrderInterface $cart)
    {
        // Set default state (for now we set it to "open"), do this last since it will persist and flush the cart
        $cartClass = $this->cartClass;
        $this->setOrderState($cart, $cartClass::STATE_OPEN);

        //Delegate further initialization of the cart to those concerned
        $eventsClass = $this->eventsClass;
        $this->eventDispatcher->dispatch($eventsClass::INIT_ORDER, $this->eventDispatcher->createEvent($cart));
    }

    protected function doAddProductToOrder(OrderInterface $cart, ProductInterface $product, $options, $quantity, $combine = true)
    {
        if ($combine && $cartItem = $this->findProductInOrder($cart, $product, $options)) {
            $quantity = $cartItem->getQuantity() + $quantity;
            $this->setItemQuantity($cartItem, $quantity);

            return $cartItem;
        }

        $cartItem = $this->createItem($product, $options);
        $this->setItemQuantity($cartItem, $quantity);

        // add item to cart
        $rm = new \ReflectionMethod($cart, 'addItem');
        $rm->setAccessible(true);
        $rm->invokeArgs($cart, array($cartItem));
        $rm->setAccessible(false);

        $eventsClass = $this->eventsClass;
        $this->eventDispatcher->dispatch($eventsClass::INIT_ITEM, $this->eventDispatcher->createEvent($cartItem));

        return $cartItem;
    }

    protected function doRemoveItemFromOrder(OrderInterface $cart, ProductInterface $product, array $options)
    {
        if ($item = $this->findProductInOrder($cart, $product, $options)) {
            $rm = new \ReflectionMethod($cart, 'removeItem');
            $rm->setAccessible(true);
            $rm->invokeArgs($cart, array($item));
            $rm->setAccessible(false);
        }
    }

    protected function initOrderItem(ItemInterface $cartItem)
    {
        if ($product = $cartItem->getProduct()) {
            $cartItem->setName($product->getName());
        }
    }

    /**
     * @param \Vespolina\Entity\Order\OrderInterface $order
     */
    public function persistOrder(OrderInterface $order)
    {
        $this->gateway->persistOrder($order);
    }

    /**
     * @param \Vespolina\Entity\Partner\PartnerInterface $partner
     * @return \Vespolina\Entity\Order\Order
     */
    public function findOpenOrderByOwner(PartnerInterface $partner)
    {
        $orderClass = $this->orderClass;

        return $this->gateway
            ->createQuery('Select')
            ->filterEqual('partner', $partner->getId())
            ->filterEqual('state', $orderClass::STATE_OPEN)
            ->one()
        ;
    }

    /**
     * @param \Vespolina\Entity\Partner\PartnerInterface $partner
     * @return \Vespolina\Entity\Order\Order
     */
    public function findClosedOrdersByOwner(PartnerInterface $partner)
    {
        $orderClass = $this->orderClass;

        return $this->gateway
            ->createQuery('Select')
            ->filterEqual('partner', $partner->getId())
            ->filterEqual('state', $orderClass::STATE_CLOSED)
            ->all()
        ;
    }

    public function clearOrder(OrderInterface $order)
    {
        $order->clearAttributes();
        foreach ($order->getItems() as $item) {
            $this->itemGateway->deleteItem($item);
        }
    }

}

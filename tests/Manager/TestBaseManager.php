<?php

namespace Vespolina\Billing\Tests\Manager;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Vespolina\Billing\Manager\BillingInvoiceManagerInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\EventInterface;
use Vespolina\Order\Manager\OrderManagerInterface;
use Vespolina\Product\Manager\ProductManagerInterface;

class TestBaseManager extends WebTestCase
{
    protected $container;
    protected $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $env = isset($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'test';

        static::$kernel = static::createKernel(array('environment' => $env));
        static::$kernel->boot();
        $this->container = static::$kernel->getContainer();
        $this->em = $this->container->get('doctrine')->getManager();

        $query = "UPDATE Vespolina\Entity\Partner\Partner p SET p.preferredPaymentProfile = NULL";
        $this->getEntityManager()->createQuery($query)->execute();

        $this->purge();

        $this->createCaraClient();

        $this->initPermissions();
        $this->initProducts();
    }

    /**
     * Check if there's queued email in the memory spool
     * @param int $expectedContents
     */
    protected function assertQueueContents($expectedContents = 1)
    {
        /** @var $transport \Swift_SpoolTransport */
        $transport = $this->getMailer()->getTransport();

        $count = $transport->getSpool()->flushQueue($this->getNewMemoryTransport());

        $this->assertEquals($expectedContents, $count);
    }

    /**
     * Create new memory transport so we can validate spool content
     * @return \Swift_SpoolTransport
     */
    protected function getNewMemoryTransport()
    {
        $newSpool = new \Swift_MemorySpool();

        $newMemoryTransport = \Swift_SpoolTransport::newInstance($newSpool);

        return $newMemoryTransport;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        if($this->em) {
            $this->em->close();
        }
    }

    public function purge()
    {
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);

        $query = "UPDATE Vespolina\Entity\Partner\Partner p SET p.preferredPaymentProfile = NULL";
        $this->getEntityManager()->createQuery($query)->execute();

        $executor->purge();

        /**
        $projectEntities = array(
        '\ImmersiveLabs\CaraCore\Entity\Tag',
        '\ImmersiveLabs\CaraCore\Entity\License',
        '\ImmersiveLabs\CaraCore\Entity\Impression',
        '\ImmersiveLabs\OAuthServerBundle\Entity\Client',
        '\ImmersiveLabs\CaraCore\Entity\User',
        );

        foreach ($projectEntities as $entityName) {
        $this->getEntityManager()
        ->createQuery(sprintf('DELETE FROM %s c', $entityName))
        ->execute()
        ;
        }
         */
    }

    /**
     * @return \Molino\Doctrine\ORM\Molino
     */
    public function getMolino()
    {
        return $this->container->get('molino');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * Compare if two arrays have the same values
     *
     * @param $a1
     * @param $a2
     * @return boolean
     */
    public function arraysAreEqual($a1, $a2) {
        return !array_diff($a1, $a2) && !array_diff($a2, $a1);
    }

    /**
     * @return \Vespolina\Symfony2Bundle\EventDispatcher\EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->container->get('vespolina.event_dispatcher');
    }

    /**
     * @return \Swift_Mailer
     */
    public function getMailer()
    {
        return $this->container->get('mailer');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    public function getSession()
    {
        return $this->container->get('session');
    }

    /**
     * @return \JMS\Payment\CoreBundle\PluginController\EntityPluginController
     */
    public function getPaymentPluginController()
    {
        return $this->container->get('payment.plugin_controller');
    }

    /**
     * @return BillingInvoiceManagerInterface
     */
    public function getBillingInvoiceManager()
    {
        return $this->container->get('vespolina.billing_invoice_manager');
    }

    /**
     * @return OrderManagerInterface
     */
    public function getOrderManager()
    {
        return $this->container->get('vespolina.order_manager');
    }

    /**
     * @return ProductManagerInterface
     */
    public function getProductManager()
    {
        return $this->container->get('vespolina.product_manager');
    }

}

class TestDispatcher implements EventDispatcherInterface
{
    protected $lastEvent;
    protected $lastEventName;

    public function createEvent($subject = null)
    {
        $event = new Event($subject);

        return $event;
    }

    public function dispatch($eventName, EventInterface $event = null)
    {
        $this->lastEvent = $event;
        $this->lastEventName = $eventName;
    }

    public function getLastEvent()
    {
        return $this->lastEvent;
    }

    public function getLastEventName()
    {
        return $this->lastEventName;
    }
}

class Event implements EventInterface
{
    protected $name;
    protected $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSubject()
    {
        return $this->subject;
    }
}

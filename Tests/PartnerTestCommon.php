<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\PartnerBundle\Tests;

use Vespolina\PartnerBundle\Model\PartnerManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class PartnerTestCommon extends WebTestCase
{
	static $kernel;
	
	/**
	 * PartnerManager
	 * @var Vespolina\PartnerBundle\Model\PartnerManager
	 */
	protected $manager;
	
	public function getKernel(array $options = array())
	{
		if (!self::$kernel) {
			self::$kernel = $this->createKernel($options);
			self::$kernel->boot();
		}

		return self::$kernel;
	}
	
	public function getManager()
	{
		if (!$this->manager) {
			$this->manager = new PartnerManager('Vespolina\\PartnerBundle\\Model\\Partner');
		}
		
		return $this->manager;
	}
}
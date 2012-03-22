<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\PartnerBundle\Model;

/**
 * PartnerManager - handles partner creation, updating, deletion, etc
 * 
 * @author Willem-Jan Zijderveld <willemjan@beeldspraak.com>
 */
class PartnerManager implements PartnerManagerInterface
{
	protected $partnerClass;
	
	public function __construct($partnerClass)
	{
		$this->partnerClass = $partnerClass;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function createPartner($type = Partner::INDIVIDUAL)
	{
		/* @var $partner Partner */
		$partner = new $this->partnerClass;
		$partner->setType($type);
		
		return $partner;
	}

	/**
	 * {@inheritdoc}
	 */
	public function findByPartnerId($partnerId)
	{
		
	}
}
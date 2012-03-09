<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\PartnerBundle\Model\Role;

use Vespolina\PartnerBundle\Model\Partner as BasePartner;

class Employee extends BasePartner
{
	protected $employeeId;
	protected $accessLevel;
}
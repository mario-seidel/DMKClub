<?php

namespace DMKClub\Bundle\MemberBundle\Tests\Unit\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use OroCRM\Bundle\ContactBundle\Entity\Contact;
use DMKClub\Bundle\MemberBundle\Accounting\SimpleProcessor;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition;

class MemberBillingTest extends \PHPUnit_Framework_TestCase {
	/**
	 */
	public function testGetPositionLabelMap(){

		$labels = "FEE Beitrag vom [STARTDATE] bis [ENDDATE]
ADMISSION Einmalige Aufnahmegebühr
FEECORRECTION Korrektur Ihres Beitrags
";
		$billing = new MemberBilling();
		$billing->setPositionLabels($labels);
		$labels = $billing->getPositionLabelMap();

		$this->assertEquals(3, count($labels));

		$this->assertEquals('Beitrag vom [STARTDATE] bis [ENDDATE]', $labels[MemberFeePosition::FLAG_FEE]);
		$this->assertEquals('Einmalige Aufnahmegebühr', $labels[MemberFeePosition::FLAG_ADMISSON]);
		$this->assertEquals('Korrektur Ihres Beitrags', $labels[MemberFeePosition::FLAG_CORRECTION]);
	}


}

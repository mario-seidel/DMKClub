<?php

namespace DMKClub\Bundle\MemberBundle\Form\Handler;

use OroCRM\Bundle\ChannelBundle\Provider\RequestChannelProvider;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\TagBundle\Entity\TagManager;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Model\ProcessorSettings;
use DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount;

class MemberFeeDiscountHandler
{
	/** @var FormInterface */
	protected $form;

	/** @var Request */
	protected $request;

	/** @var ObjectManager */
	protected $manager;

	/**
	 * @param FormInterface          $form
	 * @param Request                $request
	 * @param ObjectManager          $manager
	 * @param RequestChannelProvider $requestChannelProvider
	 */
	public function __construct(FormInterface $form, Request $request, ObjectManager $manager) {
	    $this->form              = $form;
	    $this->request           = $request;
	    $this->manager           = $manager;
	}

	/**
	 * Process form
	 *
	 * @param  MemberFeeDiscount $entity
	 *
	 * @return bool True on successful processing, false otherwise
	 */
	public function process(MemberFeeDiscount $entity)
	{
		$this->form->setData($entity);

		if (in_array($this->request->getMethod(), array('POST', 'PUT'))) {
		    $this->form->submit($this->request);

		    if ($this->form->isValid()) {
		        $this->onSuccess($entity);

		        return true;
		    }
		}

		return false;
	}

	/**
	 * "Success" form handler
	 *
	 * @param MemberFeeDiscount $entity
	 */
	protected function onSuccess(MemberFeeDiscount $entity) {
		$this->manager->persist($entity);
		$this->manager->flush();
	}

}

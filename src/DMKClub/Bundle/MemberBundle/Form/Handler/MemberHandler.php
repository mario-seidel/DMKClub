<?php

namespace DMKClub\Bundle\MemberBundle\Form\Handler;

use OroCRM\Bundle\ChannelBundle\Provider\RequestChannelProvider;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use Oro\Bundle\TagBundle\Entity\TagManager;
use Monolog\Logger;
use DMKClub\Bundle\PaymentBundle\Sepa\Iban\OpenIBAN;

class MemberHandler
{
    /** @var FormInterface */
    protected $form;

    /** @var Request */
    protected $request;

    /** @var ObjectManager */
    protected $manager;

    protected $openIban;
    protected $logger;

    /**
     * @param FormInterface          $form
     * @param Request                $request
     * @param ObjectManager          $manager
     * @param RequestChannelProvider $requestChannelProvider
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        ObjectManager $manager,
        OpenIBAN $openIban,
        Logger $logger
    ) {
        $this->form      = $form;
        $this->request   = $request;
        $this->manager   = $manager;
        $this->openIban  = $openIban;
        $this->logger    = $logger;
    }

    /**
     * Process form
     *
     * @param  Lead $entity
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process(Member $entity)
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
	 * @param Member $entity
	 */
	protected function onSuccess(Member $entity) {
	    $bankAccount = $entity->getBankAccount();
	    if($bankAccount && $bankAccount->getIban() && !$bankAccount->getBic()) {
	        try {
	            $bankData = $this->openIban->lookupBic($bankAccount->getIban());
	            if($bankData) {
	                $bankAccount->setBic($bankData->bic);
	                if(!$bankAccount->getBankName()) {
	                    $bankAccount->setBankName($bankData->name);
	                }
	            }
	        }
	        catch(\Exception $e) {
	            $this->logger->error('IBAN validation failed', ['e' => $e]);
	        }
	    }

	    $this->manager->persist($entity);
		$this->manager->flush();
		$this->tagManager->saveTagging($entity);
	}
	/**
	 * Setter for tag manager
	 *
	 * @param TagManager $tagManager
	 */
	public function setTagManager(TagManager $tagManager) {
		$this->tagManager = $tagManager;
	}
}

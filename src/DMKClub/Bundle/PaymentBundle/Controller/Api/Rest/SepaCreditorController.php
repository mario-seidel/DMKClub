<?php

namespace DMKClub\Bundle\PaymentBundle\Controller\Api\Rest;

use BadMethodCallException;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\NamePrefix;

use Symfony\Component\HttpFoundation\Response;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;

/**
 * @RouteResource("sepacreditor")
 * @NamePrefix("dmkclub_api_")
 */
class SepaCreditorController extends RestController
{
    /**
     * @param int $id
     *
     * @ApiDoc(
     *      description="Delete sepa creditors",
     *      resource=true
     * )
     * @Acl(
     *      id="dmkclub_payment_sepacreditor_delete",
     *      type="entity",
     *      permission="DELETE",
     *      class="DMKClubPaymentBundle:SepaCreditor"
     * )
     * @return Response
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormHandler()
    {
        throw new BadMethodCallException('This method is unsupported.');
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->get('dmkclub.payment.sepacreditor.manager.api');
    }
}

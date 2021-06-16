<?php
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Model\Config\Payment;

use Magento\Sales\Api\OrderRepositoryInterface;
use Trans\Sprint\Model\SprintResponseRepository;

class Sprint
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepo;

    /**
     * @var \Trans\Sprint\Model\SprintResponseRepository
     */
    protected $sprintResponseRepo;

    /**
     * Constructor
     * @param OrderRepositoryInterface $orderRepo
     * @param SprintResponseRepository $sprintResponseRepo
     */
    public function __construct(
        OrderRepositoryInterface $orderRepo, 
        SprintResponseRepository $sprintResponseRepo
    ) {
        $this->orderRepo = $orderRepo;
        $this->sprintResponseRepo = $sprintResponseRepo;
    }

    /**
     * Is payment expired
     * @param  int $orderId
     * @return boolean
     */
    public function isExpired($orderId)
    {
        $order = $this->orderRepo->get($orderId);
        $sprintResponse = $this->sprintResponseRepo->getByQuoteId($order->getQuoteId());
        $now = new \DateTime(date("Y-m-d H:i:s"));
        $expire = new \DateTime($sprintResponse->getExpireDate());
        if($now > $expire)
            return true;
        return false;
    }
}
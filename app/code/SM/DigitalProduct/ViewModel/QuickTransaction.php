<?php
/**
 * Class QuickTransaction
 * @package SM\DigitalProduct\ViewModel
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\ViewModel;

use Magento\Catalog\Helper\Image;

/**
 * Class QuickTransaction
 * @package SM\DigitalProduct\ViewModel
 */
class QuickTransaction implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \SM\DigitalProduct\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \SM\DigitalProduct\Helper\Config
     */
    private $configHelper;
    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * QuickTransaction constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \SM\DigitalProduct\Api\OrderRepositoryInterface $orderRepository
     * @param \SM\DigitalProduct\Helper\Config $configHelper
     * @param Image $imageHelper
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \SM\DigitalProduct\Api\OrderRepositoryInterface $orderRepository,
        \SM\DigitalProduct\Helper\Config $configHelper,
        Image $imageHelper
    ) {
        $this->imageHelper = $imageHelper;
        $this->configHelper = $configHelper;
        $this->customerSession = $customerSession;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return array|\Magento\Sales\Api\Data\OrderItemInterface[]
     */
    public function getList()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->orderRepository->getList(
                $this->customerSession->getId(),
                $this->configHelper->getMaxNumberTransactionToShow()
            );
        }
        return [];
    }

    /**
     * @return string
     */
    public function getPlaceHolderImage()
    {
        return $this->imageHelper->getDefaultPlaceholderUrl('image');
    }

    /**
     * @param $currentPeriod
     * @return string
     * @throws \Exception
     */
    public function getNextElectricityBillPeriod($currentPeriod)
    {
        if (strpos($currentPeriod, '-')) {
            $currentPeriod = trim(explode('-', $currentPeriod)[1]);
        }
        $datetime = new \DateTime($currentPeriod);
        $datetime->modify('next month');
        return $datetime->format('F Y');
    }
}

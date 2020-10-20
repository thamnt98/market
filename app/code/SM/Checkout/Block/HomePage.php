<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/31/20
 * Time: 10:58 AM
 */

namespace SM\Checkout\Block;

use Magento\Framework\View\Element\Template;

class HomePage extends Template
{
    public function __construct(
        Template\Context $context,
        \SM\Checkout\Helper\Config $helperConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperConfig = $helperConfig;
    }

    /**
     * @return mixed
     */
    public function isActive()
    {
        return $this->helperConfig->isActiveFulfillmentStore();
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->getBaseUrl() . $this->helperConfig->getDeliveryAreaLink();
    }
}

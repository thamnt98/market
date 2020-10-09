<?php
/**
 * @category Trans
 * @package  Trans_AllowLocation
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.cm>
 *
 * Copyright © 2019 PT CTCORP Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\AllowLocation\Block;

/**
 * Class Locationallow
 */
class Locationallow extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Trans\AllowLocation\Helper\Data 
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Trans\AllowLocation\Helper\Data $helperData
     */

    function __construct( \Magento\Framework\View\Element\Template\Context $context,
                          \Trans\AllowLocation\Helper\Data $helperData,
                          array $data = []
                        ) 
    {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Applied enable or disable module in frontend
     *
     */
    public function enabledLocation()
    {
       return $this->helperData->isEnabled();
    }

    /**
     * Get secret key id from store
     *
     */
    public function getSecret()
    {
        return $this->helperData->getSecretKey();
    }
}
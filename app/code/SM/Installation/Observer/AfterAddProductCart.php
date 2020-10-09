<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Installation
 *
 * Date: April, 21 2020
 * Time: 2:57 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Installation\Observer;

class AfterAddProductCart implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SM\Installation\Helper\Data
     */
    protected $helper;

    /**
     * BeforeSubmitQuote constructor.
     *
     * @param \SM\Installation\Helper\Data $helper
     */
    public function __construct(
        \SM\Installation\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Zend_Json_Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var \Magento\Quote\Model\Quote\Item $item */
        $item = $observer->getEvent()->getData('quote_item');
        $this->helper->updateInstallationItem($item);
    }
}

<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 29 2020
 * Time: 10:28 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Block\Header;

class Sidebar extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSessionFactory;

    /**
     * Sidebar constructor.
     *
     * @param \Magento\Customer\Model\SessionFactory           $customerSessionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSessionFactory = $customerSessionFactory;
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        return $this->customerSessionFactory->create()->isLoggedIn();
    }
}

<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Installation
 *
 * Date: April, 22 2020
 * Time: 2:33 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Installation\Controller\Actions;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \SM\Installation\Helper\Data
     */
    protected $helper;

    /**
     * Remove constructor.
     *
     * @param \SM\Installation\Helper\Data          $helper
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session       $checkoutSession
     */
    public function __construct(
        \SM\Installation\Helper\Data $helper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
    }

    /**
     * Function execute
     */
    public function execute()
    {
        $data = [];
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $itemId = $this->getRequest()->getParam('item_id');
        $action = $this->getRequest()->getParam('action');
        if (empty($action)) {
            return $resultJson->setHttpResponseCode(502)
                ->setData(['error' => 'Action is required.']);
        }

        try {
            if ($itemId) {
                $item = $this->checkoutSession->getQuote()->getItemById($itemId);
            } else {
                $item = $this->getItemByProduct();
            }

            if (!$item) {
                $data = ['error' => 'Quote Item Not Found'];
                $resultJson->setHttpResponseCode(502);
            } else {
                switch ($action) {
                    case 'remove':
                        $this->helper->removeInstallationItem($item);
                        break;
                    case 'update':
                        $this->helper->updateInstallationItem($item);
                        break;
                }

                $item->saveItemOptions();
                $resultJson->setHttpResponseCode(200);
            }
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];
            $resultJson->setHttpResponseCode(502);
        }

        $resultJson->setData($data);

        return $resultJson;
    }

    /**
     * @return bool|\Magento\Quote\Api\Data\CartItemInterface|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getItemByProduct()
    {
        $productId = $this->getRequest()->getParam('product_id');
        if (!$productId) {
            return false;
        }

        foreach ($this->checkoutSession->getQuote()->getItems() as $item) {
            if ($item->getProductId() == $productId) {
                return $item;
            }
        }

        return false;
    }
}

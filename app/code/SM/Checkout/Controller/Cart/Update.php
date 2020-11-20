<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/24/20
 * Time: 11:03 AM
 */

namespace SM\Checkout\Controller\Cart;

use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class Update extends \Magento\Checkout\Controller\Cart implements HttpPostActionInterface
{
    const ITEM_IS_ACTIVE = 1;
    const ITEM_IS_INACTIVE = 0;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Api\CartItemRepositoryInterface
     */
    private $cartItemRepository;

    /**
     * Update constructor.
     * @param Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Api\CartItemRepositoryInterface $cartItemRepository
     * @param CustomerCart $cart
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\CartItemRepositoryInterface $cartItemRepository,
        CustomerCart $cart
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->quoteRepository = $quoteRepository;
        $this->cartItemRepository = $cartItemRepository;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $resultJson = $this->resultJsonFactory->create();
        $response = [
            'status' => 'success'
        ];

        try {
            $this->setQuote($this->_checkoutSession->getQuote());
            if (isset($data['selected-all'])) {
                $checked = self::ITEM_IS_INACTIVE;
                if ($data['selected-all'] == self::ITEM_IS_ACTIVE) {
                    $checked = self::ITEM_IS_ACTIVE;
                }

                $this->updateAll((int) $checked);
            }
            /**
             * remove items
             */
            $removeIds = isset($data['remove_ids']) ? explode(',', $data['remove_ids']) : [];
            if (!empty($removeIds)) {
                if (!$this->_formKeyValidator->validate($this->getRequest())) {
                    return $this->_goBack();
                }
                foreach ($removeIds as $id) {
                    $this->getQuote()->removeItem($id);
                }
                $this->getQuote()->setData('totals_collected_flag', false);
                $this->quoteRepository->save($this->getQuote());
                $this->messageManager->addSuccessMessage(__('Deleted items successfully!'));

                return $this->_goBack();
            }

            if (isset($data['itemId'])) {
                $this->updateItem($data['itemId']);
            }

            $this->getQuote()->setData('totals_collected_flag', false);
            $this->quoteRepository->save($this->getQuote());
            $this->messageManager->getMessages(true);
            $response['message'] = '';
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $response['message'] = $e->getMessage();
            $response['status'] = __('Error');
        }
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
        return $resultJson->setData($response);
    }

    /**
     * @param string $data
     */
    protected function updateItem($data)
    {
        [$itemId, $isActive] = explode('=', $data);
        if ($item = $this->getQuote()->getItemById($itemId)) {
            $isActive = (int) $isActive;
            $item->setData('is_active', $isActive);
            foreach ($item->getChildren() as $child) {
                $child->setData('is_active', $isActive);
            }
        }
    }

    /**
     * @param $checked
     */
    protected function updateAll($checked)
    {
        foreach ($this->getQuote()->getItemsCollection() as $item) {
            $item->setData('is_active', $checked);
        }
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        return $this->quote;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     */
    protected function setQuote($quote)
    {
        $this->quote = $quote;
    }
}

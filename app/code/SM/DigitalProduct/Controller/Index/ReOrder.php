<?php
/**
 * Class ReOrder
 * @package SM\DigitalProduct\Controller\Index
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Controller\Index;

use Magento\Framework\App\Action\Context;

class ReOrder extends AbstractAddToCart
{
    /**
     * @var \SM\DigitalProduct\Api\ReorderRepositoryInterface
     */
    private $reorderRepository;

    /**
     * ReOrder constructor.
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemFactory
     * @param \Magento\Quote\Api\Data\ProductOptionInterfaceFactory $productOptionFactory
     * @param \Magento\Quote\Api\Data\ProductOptionExtensionInterfaceFactory $productOptionExtensionFactory
     * @param \SM\DigitalProduct\Model\Cart\CartRepository $cartRepository
     * @param \SM\DigitalProduct\Api\Data\DigitalInterfaceFactory $digitalDataFactory
     * @param \SM\DigitalProduct\Api\Data\DigitalTransactionInterfaceFactory $digitalTransactionDataFactory
     * @param \SM\DigitalProduct\Api\ReorderRepositoryInterface $reorderRepository
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemFactory,
        \Magento\Quote\Api\Data\ProductOptionInterfaceFactory $productOptionFactory,
        \Magento\Quote\Api\Data\ProductOptionExtensionInterfaceFactory $productOptionExtensionFactory,
        \SM\DigitalProduct\Model\Cart\CartRepository $cartRepository,
        \SM\DigitalProduct\Api\Data\DigitalInterfaceFactory $digitalDataFactory,
        \SM\DigitalProduct\Api\Data\DigitalTransactionInterfaceFactory $digitalTransactionDataFactory,
        \SM\DigitalProduct\Api\ReorderRepositoryInterface $reorderRepository
    ) {
        $this->reorderRepository = $reorderRepository;
        parent::__construct(
            $context,
            $checkoutSession,
            $formKeyValidator,
            $cartItemFactory,
            $productOptionFactory,
            $productOptionExtensionFactory,
            $cartRepository,
            $digitalDataFactory,
            $digitalTransactionDataFactory
        );
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(
                __('Your session has expired')
            );
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }

        if ($data = $this->getRequest()->getParams()) {
            try {
                $quote = $this->checkoutSession->getQuote();
                $this->reorderRepository->reOrder(
                    $quote->getCustomerId(),
                    $quote->getId(),
                    $this->prepareData($data),
                    $quote
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                return $this->getServiceTypeUrl($data['service_type']);
            }
        }

        return $this->redirectToCheckout();
    }

    /**
     * @param $serviceType
     * @return string
     */
    protected function getServiceTypeUrl($serviceType)
    {
        return $this->resultRedirectFactory->create()
            ->setPath(
                $this->getRequest()->getModuleName() . "/" . explode('_', $serviceType)[0]
            );
    }
}

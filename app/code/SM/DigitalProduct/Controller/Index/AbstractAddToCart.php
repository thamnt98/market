<?php
/**
 * Class AbstractAddToCart
 * @package SM\DigitalProduct\Controller\Index
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Phrase;

abstract class AbstractAddToCart extends Action implements CsrfAwareActionInterface, HttpPostActionInterface
{
    const DIGITAL_CHECKOUT_PATH = 'transcheckout/digitalproduct';

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \SM\DigitalProduct\Model\Cart\CartRepository
     */
    protected $cartRepository;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Api\Data\CartItemInterfaceFactory
     */
    protected $cartItemFactory;

    /**
     * @var \Magento\Quote\Api\Data\ProductOptionInterfaceFactory
     */
    protected $productOptionFactory;

    /**
     * @var \Magento\Quote\Api\Data\ProductOptionExtensionInterfaceFactory
     */
    protected $productOptionExtensionFactory;

    /**
     * @var \SM\DigitalProduct\Api\Data\DigitalInterfaceFactory
     */
    protected $digitalDataFactory;

    /**
     * @var \SM\DigitalProduct\Api\Data\DigitalTransactionInterfaceFactory
     */
    protected $digitalTransactionDataFactory;

    /**
     * AddToCart constructor.
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemFactory
     * @param \Magento\Quote\Api\Data\ProductOptionInterfaceFactory $productOptionFactory
     * @param \Magento\Quote\Api\Data\ProductOptionExtensionInterfaceFactory $productOptionExtensionFactory
     * @param \SM\DigitalProduct\Model\Cart\CartRepository $cartRepository
     * @param \SM\DigitalProduct\Api\Data\DigitalInterfaceFactory $digitalDataFactory
     * @param \SM\DigitalProduct\Api\Data\DigitalTransactionInterfaceFactory $digitalTransactionDataFactory
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
        \SM\DigitalProduct\Api\Data\DigitalTransactionInterfaceFactory $digitalTransactionDataFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->cartRepository = $cartRepository;
        $this->cartItemFactory = $cartItemFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionExtensionFactory = $productOptionExtensionFactory;
        $this->digitalDataFactory = $digitalDataFactory;
        $this->digitalTransactionDataFactory = $digitalTransactionDataFactory;
        parent::__construct($context);
    }

    /**
     * @param $data
     * @return \Magento\Quote\Model\Quote\Item
     */
    protected function prepareData($data)
    {
        $productOptionExtension = $this->productOptionExtensionFactory->create();

        $this->setDigitalData($data, $productOptionExtension);

        $data['product_option'] = $this->productOptionFactory
            ->create()
            ->setExtensionAttributes($productOptionExtension);

        $cartItem = $this->cartItemFactory->create();
        return $cartItem->setData($data);
    }

    /**
     * @param $productOptionExtension
     */
    protected function setDigitalData($data, $productOptionExtension)
    {
        if (isset($data['digital'])) {
            $digitalData = $this->digitalDataFactory
                ->create()
                ->setData($data['digital']);
            $productOptionExtension->setDigital($digitalData);
        }

        if (isset($data['service_type'])) {
            $productOptionExtension->setServiceType($data['service_type']);
        }

        if (isset($data['digital_transaction'])) {
            $digitalData = $this->digitalTransactionDataFactory
                ->create()
                ->setData($data['digital_transaction']);
            $productOptionExtension->setDigitalTransaction($digitalData);
        }
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        /**
         * @var Redirect $resultRedirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/edit');

        return new InvalidRequestException(
            $resultRedirect,
            [new Phrase('Invalid Form Key. Please refresh the page.')]
        );
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return null;
    }

    /**
     * @return Redirect
     */
    public function redirectToCheckout()
    {
        return $this->resultRedirectFactory->create()->setPath(self::DIGITAL_CHECKOUT_PATH);
    }
}

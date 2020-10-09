<?php
/**
 * Class AddToCart
 * @package SM\DigitalProduct\Controller\Index
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Controller\Index;

class AddToCart extends AbstractAddToCart
{
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
                $this->cartRepository->addToCart(
                    $quote->getId(),
                    $this->prepareData($data),
                    $quote
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
            }

            return $this->redirectToCheckout();
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
    }
}

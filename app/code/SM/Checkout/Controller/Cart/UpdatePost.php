<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/24/20
 * Time: 11:03 AM
 */

namespace SM\Checkout\Controller\Cart;

class UpdatePost extends \Magento\Checkout\Controller\Cart\UpdatePost
{
    /**
     * Update shopping cart data action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }

        return $this->resultRedirectFactory->create()->setPath('transcheckout/cart/item');
    }
}

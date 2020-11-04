<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/24/20
 * Time: 11:03 AM
 */

namespace SM\Checkout\Controller\Cart;

use Magento\Framework\Controller\ResultFactory;

class Item extends \Magento\Framework\App\Action\Action
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}

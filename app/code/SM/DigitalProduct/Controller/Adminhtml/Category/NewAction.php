<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Adminhtml\Category;

use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultInterface;
use SM\DigitalProduct\Controller\Adminhtml\Category;

/**
 * Class NewAction
 * @package SM\DigitalProduct\Controller\Adminhtml\Category
 */
class NewAction extends Category
{
    /**
     * New action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}

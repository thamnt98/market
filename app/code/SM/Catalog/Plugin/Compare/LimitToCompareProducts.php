<?php

namespace SM\Catalog\Plugin\Compare;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Helper\Product\Compare;

class LimitToCompareProducts
{
    const LIMIT_TO_COMPARE_PRODUCTS = 3;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /** @var Compare */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\Product\Compare\ListCompare
     */
    protected $catalogProductCompareList;

    /**
     * RestrictCustomerEmail constructor.
     * @param Compare $helper
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        RedirectFactory $redirectFactory,
        Compare $helper,
        ManagerInterface $messageManager,
        \Magento\Catalog\Model\Product\Compare\ListCompare $catalogProductCompareList)
    {
        $this->helper = $helper;
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->catalogProductCompareList = $catalogProductCompareList;
    }

    /**
     * @param \Magento\Catalog\Controller\Product\Compare\Add $subject
     * @param \Closure $proceed
     * @return $this|mixed
     */
    public function aroundExecute(
        \Magento\Catalog\Controller\Product\Compare\Add $subject,
        \Closure $proceed
    ){
        $addProductId =  $subject->getRequest()->getParam("product");
        $allowRemove = $subject->getRequest()->getParam('removeIds');
        $count = $this->helper->getItemCount();
        $removeList = explode("-",$allowRemove);
        $comepareList = $this->helper->getItemCollection();

        foreach ($comepareList as $product){
            if($product->getId() == $addProductId){
                $message = "This product already exist in compare list";
                $this->messageManager->addErrorMessage($message);

                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setRefererOrBaseUrl();
            }
        }

        if(isset($allowRemove) && !empty($removeList)){
            $tmp = $comepareList;
            foreach ($tmp as $product){
                if(in_array($product->getId(),$removeList)){
                    $this->catalogProductCompareList->removeProduct($product);
                }
            }
        }
        else if($count >= self::LIMIT_TO_COMPARE_PRODUCTS) {
            $message = "You comparision list already full (3 items)";
            $this->messageManager->addErrorMessage($message);

            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

        return $proceed();
    }
}
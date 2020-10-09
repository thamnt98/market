<?php
/**
 * SM\Catalog\Controller\Ajax
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\Catalog\Controller\Ajax;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class GetStorePickup
 * @package SM\Catalog\Controller\Ajax
 */
class GetStorePickup extends Action
{
    const STORE_PICKUP_TEMPLATE = 'Magento_Catalog::product/view/pdp/store-pickup.phtml';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * GetStorePickup constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultPage = $this->resultPageFactory->create();

        try {
            $data = $this->getRequest()->getPostValue();
            $productId = $data["product_id"];

            $block = $resultPage->getLayout()
                ->createBlock(\SM\Catalog\Block\Product\View::class)
                ->setProductId($productId)
                ->setTemplate(self::STORE_PICKUP_TEMPLATE)
                ->toHtml();
            $resultJson->setData([
                "status" => 1,
                "html" => $block
            ]);
            return $resultJson;
        } catch (\Exception $e) {
            $resultJson->setData([
                "status" => 0,
                "message" => $e->getMessage()
            ]);
            return $resultJson;
        }
    }
}

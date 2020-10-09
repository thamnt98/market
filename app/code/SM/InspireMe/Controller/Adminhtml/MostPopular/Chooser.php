<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Controller\Adminhtml\MostPopular;

use Magento\Backend\App\Action;
use Mirasvit\Blog\Api\Data\PostInterface;
use Mirasvit\Blog\Model\ResourceModel\Post\Collection;
use Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory;

/**
 * Class Chooser
 * @package SM\InspireMe\Controller\Adminhtml\MostPopular
 */
class Chooser extends \Magento\Backend\App\Action
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Chooser constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $name = $this->getRequest()->getParam('name');

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(PostInterface::NAME, ['like' => "%$name%"])
            ->addFieldToFilter(PostInterface::STATUS, ['eq' => PostInterface::STATUS_PUBLISHED])
            ->setOrder(PostInterface::CREATED_AT, 'desc')
            ->setPageSize(5);

        if (!$collection->getSize()) {
            $response = [
                'error' => true,
                'message' => __('Collection empty'),
            ];
        } else {

            $listArticleName = [];
            /** @var \Mirasvit\Blog\Model\Post $item */
            foreach ($collection as $item) {
                $listArticleName[] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                ];
            }

            $response = [
                'error' => false,
                'articles' => $listArticleName,
            ];
        }

        //TODO: Save collection to local storage
        return $resultJson->setData($response);
    }
}

<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 30 2020
 * Time: 6:18 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Controller\Adminhtml\Index;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $resource;

    /**
     * MassDelete constructor.
     *
     * @param \Magento\Backend\App\Action\Context                                 $context
     * @param \Magento\Ui\Component\MassAction\Filter                             $filter
     * @param \SM\Notification\Model\ResourceModel\Notification                   $resource
     * @param \SM\Notification\Model\ResourceModel\Notification\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \SM\Notification\Model\ResourceModel\Notification $resource,
        \SM\Notification\Model\ResourceModel\Notification\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \SM\Notification\Model\ResourceModel\Notification\Collection $collection */
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $success = 0;
        $failed = 0;
        /** @var \SM\Notification\Model\Notification $item */
        foreach ($collection as $item) {
            try {
                $this->resource->delete($item);
                $success++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        if ($success > 0) {
            $this->messageManager->addSuccessMessage(
                __(
                    '%1 of %2 notification(s) have been deleted.',
                    $success,
                    $success + $failed
                )
            );
        }

        if ($failed > 0) {
            $this->messageManager->addErrorMessage(
                __(
                    '%1 total of %2 notification(s) have been deleted.',
                    $failed,
                    $collection->getSize()
                )
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}

<?php
/**
 * @category Trans
 * @package  Trans_Reservation
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Controller\Adminhtml\Channel;
 
use Magento\Framework\Exception\LocalizedException;
use Trans\Reservation\Api\Data\ReservationConfigInterface;
 
/**
 * Class Delete
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Trans_Integration::integrationchannel';

    /**
     * @var \Trans\Integration\Api\IntegrationChannelRepositoryInterface
     */
    protected $channelRepository;
 
    /**
     * @var \Trans\Integration\Api\Data\IntegrationChannelInterfaceFactory
     */
    protected $channelFactory;
 
    /**
     * @var Logger
     */
    protected $logger;
 
    /**
     * @param Context $context
     * @param \Trans\Integration\Logger\Logger $logger
     * @param \Trans\Integration\Api\IntegrationChannelRepositoryInterface $channelRepository
     * @param \Trans\Integration\Api\Data\IntegrationChannelInterfaceFactory $channelFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Trans\Integration\Logger\Logger $logger,
        \Trans\Integration\Api\IntegrationChannelRepositoryInterface $channelRepository,
        \Trans\Integration\Api\Data\IntegrationChannelInterfaceFactory $channelFactory
    )
    {
        $this->channelRepository = $channelRepository;
        $this->channelFactory = $channelFactory;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * get init data
     * 
     * @return \Trans\Integration\Api\Data\IntegrationChannelInterface
     */
    protected function initData()
    {
        $channelId = $this->getRequest()->getParam('id');

        try {
            $method = $this->channelRepository->getById($channelId);
        } catch (NoSuchEntityException $e) {
            $method = $this->channelFactory->create();
        }

        return $method;
    }
 
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            $model = $this->initData();
            
            try {
                $this->logger->info("=========== Start Delete Data =============");
                $this->channelRepository->delete($model);
                $this->messageManager->addSuccessMessage(__('You deleted data.'));
                $this->logger->info("success");
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->info("Error delete data. Message = " . $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting data.'));
                $this->logger->info("Error delete data. Message = " . $e->getMessage());
            }
            
            $this->logger->info("===========End Delete Data=============");
        }

        return $resultRedirect->setPath('*/*/');
    }
}

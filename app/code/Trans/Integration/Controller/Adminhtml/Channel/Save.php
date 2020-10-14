<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Controller\Adminhtml\Channel;
 
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Trans\Integration\Api\Data\IntegrationChannelInterface;

/**
 * Class Save
 */
class Save extends \Magento\Backend\App\Action
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
     * @param \Trans\Integration\Api\Data\IntegrationChannelInterface $channelFactory
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
        $channelid = $this->getRequest()->getParam('id');

        try {
            $channel = $this->channelRepository->getById($channelid);
        } catch (NoSuchEntityException $e) {
            $channel = $this->channelFactory->create();
        }

        return $channel;
    }

    /**
     * save data
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getParams();
        
        if($data) {
            $model = $this->initData();
            
            $model->setName($data[IntegrationChannelInterface::NAME]);
            $model->setUrl($data[IntegrationChannelInterface::URL]);
            $model->setEnvironment($data[IntegrationChannelInterface::ENV]);
            $model->setStatus($data[IntegrationChannelInterface::STATUS]);

            try {
                $this->logger->info("===========Start Save Channel=============");
                $this->channelRepository->save($model);             

                $this->messageManager->addSuccessMessage(__('You saved the channel.'));
                $this->logger->info("success");
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->info("Error save channel. Message = " . $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the channel.'));
                $this->logger->info("Error save channel. Message = " . $e->getMessage());
            }
            
            $this->logger->info("===========End Save Channel=============");

        }

        return $resultRedirect->setPath('*/*/');
    }
}

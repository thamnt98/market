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

namespace Trans\Integration\Controller\Adminhtml\ChannelMethod;
 
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Trans\Integration\Api\Data\IntegrationChannelMethodInterface;

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
    const ADMIN_RESOURCE = 'Trans_Integration::integrationchannelmethod';

    /**
     * @var \Trans\Integration\Api\IntegrationChannelMethodRepositoryInterface
     */
    protected $methodRepository;
 
    /**
     * @var \Trans\Integration\Api\Data\IntegrationChannelMethodInterfaceFactory
     */
    protected $methodFactory;
 
    /**
     * @var Logger
     */
    protected $logger;
 
    /**
     * @param Context $context
     * @param \Trans\Integration\Logger\Logger $logger
     * @param \Trans\Integration\Api\IntegrationChannelMethodRepositoryInterface $methodRepository
     * @param \Trans\Integration\Api\Data\IntegrationChannelMethodInterfaceFactory $methodFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Trans\Integration\Logger\Logger $logger,
        \Trans\Integration\Api\IntegrationChannelMethodRepositoryInterface $methodRepository,
        \Trans\Integration\Api\Data\IntegrationChannelMethodInterfaceFactory $methodFactory
    )
    {
        $this->methodRepository = $methodRepository;
        $this->methodFactory = $methodFactory;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * get init data
     * 
     * @return \Trans\Integration\Api\Data\IntegrationChannelMethodInterface
     */
    protected function initData()
    {
        $channelid = $this->getRequest()->getParam('id');

        try {
            $channel = $this->methodRepository->getById($channelid);
        } catch (NoSuchEntityException $e) {
            $channel = $this->methodFactory->create();
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
            
            $model->setChId($data[IntegrationChannelMethodInterface::CHANNEL_ID]);
            $model->setTag($data[IntegrationChannelMethodInterface::TAG]);
            $model->setDataDesc($data[IntegrationChannelMethodInterface::DESCRIPTION]);
            $model->setDataMethod($data[IntegrationChannelMethodInterface::METHOD]);
            $model->setDataHeaders($data[IntegrationChannelMethodInterface::HEADERS]);
            $model->setQueryParams($data[IntegrationChannelMethodInterface::QUERY_PARAMS]);
            $model->setDataBody($data[IntegrationChannelMethodInterface::BODY]);
            $model->setDataPath($data[IntegrationChannelMethodInterface::PATH]);
            $model->setLimits($data[IntegrationChannelMethodInterface::LIMIT]);
            $model->setStatus($data[IntegrationChannelMethodInterface::STATUS]);

            try {
                $this->logger->info("===========Start Save Channel=============");
                $this->methodRepository->save($model);             

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

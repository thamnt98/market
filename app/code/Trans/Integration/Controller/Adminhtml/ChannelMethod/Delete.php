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

namespace Trans\Integration\Controller\Adminhtml\ChannelMethod;
 
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
        $methodid = $this->getRequest()->getParam('id');

        try {
            $method = $this->methodRepository->getById($methodid);
        } catch (NoSuchEntityException $e) {
            $method = $this->methodFactory->create();
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
                $this->methodRepository->delete($model);
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

        return $resultRedirect->setPath('*/*/index');
    }
}

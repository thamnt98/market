<?php

namespace SM\Review\Controller\Adminhtml\Edit;

use Magento\Backend\App\Action;
use SM\Review\Api\ReviewEditRepositoryInterface;

/**
 * Class Approve
 * @package SM\Review\Controller\Adminhtml\Edit
 */
class Approve extends Action
{
    /**
     * @var ReviewEditRepositoryInterface
     */
    protected $reviewEditRepository;

    /**
     * Approve constructor.
     * @param Action\Context $context
     * @param ReviewEditRepositoryInterface $reviewEditRepository
     */
    public function __construct(
        Action\Context $context,
        ReviewEditRepositoryInterface $reviewEditRepository
    ) {
        $this->reviewEditRepository = $reviewEditRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $entityId = $this->getRequest()->getParam("id");
        $reviewEdit = $this->reviewEditRepository->getById($entityId);

        if ($this->reviewEditRepository->apply($reviewEdit)) {
            $this->messageManager->addSuccessMessage(__("Edit has been approved successfully"));
            $redirectUrl = $this->getUrl("*/edit/pending");
        } else {
            $this->messageManager->addErrorMessage(__("Failed to approve edit"));
            $redirectUrl = $this->getUrl("*/customer/edit", ["id" => $entityId]);
        }

        return $this->_redirect($redirectUrl);
    }
}

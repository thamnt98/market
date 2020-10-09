<?php

namespace SM\Review\Controller\Adminhtml\Edit;

use Magento\Backend\App\Action;
use SM\Review\Api\Data\ReviewEditInterface;
use SM\Review\Api\ReviewEditRepositoryInterface;

/**
 * Class Reject
 * @package SM\Review\Controller\Adminhtml\Edit
 */
class Reject extends Action
{
    /**
     * @var ReviewEditRepositoryInterface
     */
    protected $reviewEditRepository;

    /**
     * Reject constructor.
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
     * @inheritDoc
     */
    public function execute()
    {
        $entityId = $this->getRequest()->getParam("id");
        /** @var ReviewEditInterface $reviewEdit */
        $reviewEdit = $this->reviewEditRepository->getById($entityId);
        if (!is_null($reviewEdit)) {
            $this->reviewEditRepository->reject($reviewEdit);
            $this->messageManager->addSuccessMessage(__("Edit rejected successfully"));
            $redirectUrl = $this->getUrl("*/edit/pending");
        } else {
            $this->messageManager->addErrorMessage(__("Failed to reject edit"));
            $redirectUrl = $this->getUrl("*/customer/edit", ["id" => $entityId]);
        }

        return $this->_redirect($redirectUrl);
    }
}

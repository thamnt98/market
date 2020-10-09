<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Adminhtml\OperatorIcon;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultInterface;
use SM\DigitalProduct\Controller\Adminhtml\OperatorIcon;
use SM\DigitalProduct\Model\OperatorIconFactory;
use SM\DigitalProduct\Model\OperatorIconRepository;

/**
 * Class NewAction
 * @package SM\DigitalProduct\Controller\Adminhtml\OperatorIcon
 */
class NewAction extends OperatorIcon
{
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param OperatorIconFactory $modelFactory
     * @param OperatorIconRepository $repository
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        OperatorIconFactory $modelFactory,
        OperatorIconRepository $repository
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $modelFactory, $repository);
    }

    /**
     * New action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}

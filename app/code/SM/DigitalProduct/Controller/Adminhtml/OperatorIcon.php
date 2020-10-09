<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;

use SM\DigitalProduct\Model\OperatorIconFactory;
use SM\DigitalProduct\Model\OperatorIconRepository;

/**
 * Class OperatorIcon
 * @package SM\DigitalProduct\Controller\Adminhtml
 */
abstract class OperatorIcon extends Action
{
    const ADMIN_RESOURCE = 'SM_DigitalProduct::top_level';
    /**
     * @var OperatorIconRepository
     */
    protected $repository;
    /**
     * @var OperatorIconFactory
     */
    protected $modelFactory;

    /**
     * @param Context $context
     * @param OperatorIconFactory $modelFactory
     * @param OperatorIconRepository $repository
     */
    public function __construct(
        Context $context,
        OperatorIconFactory $modelFactory,
        OperatorIconRepository $repository
    ) {
        $this->modelFactory = $modelFactory;
        $this->repository = $repository;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('SM'), __('SM'))
            ->addBreadcrumb(__('Operator Icon'), __('Operator Icon'));
        return $resultPage;
    }
}

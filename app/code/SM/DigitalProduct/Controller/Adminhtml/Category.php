<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use SM\DigitalProduct\Helper\Category\Data;
use SM\DigitalProduct\Model\CategoryFactory;
use SM\DigitalProduct\Model\CategoryContentFactory;
use SM\DigitalProduct\Model\CategoryRepository;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Category
 * @package SM\DigitalProduct\Controller\Adminhtml
 */
abstract class Category extends Action
{
    const ADMIN_RESOURCE = 'SM_DigitalProduct::top_level';
    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CategoryContentFactory
     */
    protected $categoryContentFactory;

    /**
     * @var DataPersistorInterface

     */
    protected $dataPersistor;

    /**
     * @var Data
     */
    protected $typeHelper;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CategoryFactory $categoryFactory
     * @param CategoryRepository $categoryRepository
     * @param CategoryContentFactory $categoryContentFactory
     * @param DataPersistorInterface $dataPersistor
     * @param ForwardFactory $resultForwardFactory
     * @param Data $typeHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CategoryFactory $categoryFactory,
        CategoryRepository $categoryRepository,
        CategoryContentFactory $categoryContentFactory,
        DataPersistorInterface $dataPersistor,
        ForwardFactory $resultForwardFactory,
        Data $typeHelper
    ) {
        $this->typeHelper = $typeHelper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->dataPersistor = $dataPersistor;
        $this->categoryContentFactory = $categoryContentFactory;
        $this->categoryRepository = $categoryRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryFactory = $categoryFactory;
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
            ->addBreadcrumb(__('Category'), __('Category'));
        return $resultPage;
    }
}

<?php
/**
 * Class ProductsGrid
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\Framework\View\Result\LayoutFactory;

/**
 * Class ProductsGrid
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class ProductsGrid extends \Magento\Backend\App\Action
{

    /**
     * LayoutFactory
     *
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * ProductsGrid constructor.
     *
     * @param Action\Context $context             context
     * @param LayoutFactory  $resultLayoutFactory resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * ProductsGrid Action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()
            ->getBlock('brand.edit.tab.products')
            ->setInProducts(
                $this->getRequest()->getPost('brand_products', null)
            );

        return $resultLayout;
    }
}

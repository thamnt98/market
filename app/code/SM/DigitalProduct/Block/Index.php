<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SM\DigitalProduct\Api\Data\CategoryInterface;
use SM\DigitalProduct\Api\Data\CategoryContentInterface;
use SM\DigitalProduct\Helper\Config;
use SM\DigitalProduct\Model\CategoryRepository;
use SM\DigitalProduct\Model\DigitalProductRepository;

/**
 * Class Index
 * @package SM\DigitalProduct\Block
 */
abstract class Index extends Template
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var Config
     */
    protected $configHelper;
    /**
     * @var DigitalProductRepository
     */
    protected $digitalProductRepository;

    /**
     * @var CategoryContentInterface
     */
    private $categoryContent;
    /**
     * Constructor
     *
     * @param Context $context
     * @param CategoryRepository $categoryRepository
     * @param Config $configHelper
     * @param DigitalProductRepository $digitalProductRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        CategoryRepository $categoryRepository,
        Config $configHelper,
        DigitalProductRepository $digitalProductRepository,
        array $data = []
    ) {
        $this->digitalProductRepository = $digitalProductRepository;
        $this->configHelper = $configHelper;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return CategoryContentInterface
     */
    public function getCategoryContent()
    {
        return $this->categoryContent;
    }

    /**
     * @param mixed $categoryContent
     * @return $this
     */
    public function setCategoryContent($categoryContent)
    {
        $this->categoryContent = $categoryContent;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('digitalproduct/index/addtocart');
    }
}

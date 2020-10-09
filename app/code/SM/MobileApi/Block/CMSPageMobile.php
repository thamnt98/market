<?php
namespace SM\MobileApi\Block;

use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

/**
 * Class CMSPageMobile
 * @package SM\MobileApi\Block
 */
class CMSPageMobile extends Template
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * CMSPageMobile constructor.
     * @param FilterProvider $filterProvider
     * @param PageFactory $pageFactory
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        FilterProvider $filterProvider,
        PageFactory $pageFactory,
        Template\Context $context,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        $this->pageFactory = $pageFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param $pageId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getContentById($pageId)
    {
        $page = $this->pageFactory->create();
        $page->load($pageId);
        if (!$page->getId()) {
            throw new NoSuchEntityException(__('The CMS page with the "%1" ID doesn\'t exist.', $pageId));
        }
        return $page->getContent();
    }

    /**
     * @param $content
     * @return string
     * @throws \Exception
     */
    public function getContentWYSIWYG($content)
    {
        return $this->filterProvider->getBlockFilter()->filter($content);
    }
}

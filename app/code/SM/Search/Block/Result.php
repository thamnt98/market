<?php

namespace SM\Search\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\CatalogSearch\Block\Result as ResultBase;
use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\QueryFactory;
use SM\Search\Helper\Config;

class Result extends ResultBase
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Result constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Registry $registry
     * @param Config $config
     * @param Context $context
     * @param LayerResolver $layerResolver
     * @param Data $catalogSearchData
     * @param QueryFactory $queryFactory
     * @param array $data
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        Registry $registry,
        Config $config,
        Context $context,
        LayerResolver $layerResolver,
        Data $catalogSearchData,
        QueryFactory $queryFactory,
        array $data = []
    ) {
        parent::__construct($context, $layerResolver, $catalogSearchData, $queryFactory, $data);
        $this->categoryRepository = $categoryRepository;
        $this->registry = $registry;
        $this->config = $config;
    }

    protected function _prepareLayout()
    {
        $titlePage = $this->getSearchQueryText();
        $title = $this->catalogSearchData->getEscapedQueryText();
        $this->pageConfig->getTitle()->set($titlePage);
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $categoryID = null;
            if ($this->getRequest()) {
                $categoryID = $this->getRequest()->getParam('cat');
            }
            if ($categoryID) {
                $category = $this->categoryRepository->get($categoryID, $this->_storeManager->getStore()->getId());
                if ($category) {
                    $url = $category->getUrl();
                    $categoryName = $category->getName();
                    $breadcrumbs->addCrumb(
                        'category',
                        [
                            'label' => __($categoryName),
                            'title' => __($categoryName),
                            'link' => $url
                        ]
                    );
                }
            }
            $breadcrumbs->addCrumb(
                'search',
                ['label' => $title, 'title' => $title]
            );
        }
    }

    public function removeChildAdditional(): void
    {
        $this->getLayout()->unsetChild('search_result_list', 'additional');
    }

    public function prepareSuggestionList(): void
    {
        $suggestionKeyword = (string) $this->registry->registry(Config::SUGGESTION_KEYWORD);
        if ($suggestionKeyword) {
            /** @var \Amasty\Shopby\Model\ResourceModel\Fulltext\Collection $collection */
            $collection = $this->_getProductCollection();
            $collection->clear();
            $collection->resetSearchEngineResult();
            $collection->setQueryText($suggestionKeyword);
            $collection->load();
        }
    }

    /**
     * @return string
     */
    public function getSuggestionKeyword(): string
    {
        return (string) $this->registry->registry(Config::SUGGESTION_KEYWORD);
    }

    /**
     * @return null|int
     */
    public function prepareRecommendationListOnNoResult(): ?int
    {
        $recommendCategoryId = $this->config->getSuggestCategoryIdOnNoResult();
        if ($recommendCategoryId) {
            try {
                $category = $this->categoryRepository->get($recommendCategoryId, $this->_storeManager->getStore()->getId());/** @var \Amasty\Shopby\Model\ResourceModel\Fulltext\Collection $collection */

                $collection = $this->_getProductCollection();
                $collection->clear();
                $collection->resetSearchEngineResult();
                $collection->setQueryText('');
                $collection->addCategoryFilter($category);
                $collection->load();

                return $collection->getSize();
            } catch (NoSuchEntityException $exception) {
                return null;
            }
        }
    }
}

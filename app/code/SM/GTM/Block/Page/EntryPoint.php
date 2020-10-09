<?php

namespace SM\GTM\Block\Page;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\Data as CatalogHelper;
use SM\GTM\Model\Data\CollectorComposite;
use SM\GTM\Model\Template\Finder;
use SM\GTM\Model\Variable\Processor as TemplateProcessor;

/**
 * Class EntryPoint
 * @package SM\GTM\Block\Page
 * @method string getCurrentLayoutHandler()
 */
class EntryPoint extends Template
{
    private $templateFinder;

    /**
     * @var TemplateProcessor
     */
    private $templateProcessor;

    /**
     * @var CollectorComposite
     */
    private $collectors;
    /**
     * @var CatalogHelper
     */
    private $catalogHelper;

    /**
     * EntryPoint constructor.
     * @param CatalogHelper $catalogHelper
     * @param CollectorComposite $collectorComposite
     * @param TemplateProcessor $templateProcessor
     * @param Finder $templateFinder
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        CatalogHelper $catalogHelper,
        CollectorComposite $collectorComposite,
        TemplateProcessor $templateProcessor,
        Finder $templateFinder,
        Template\Context $context,
        array $data = []
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->collectors = $collectorComposite;
        $this->templateProcessor = $templateProcessor;
        $this->templateFinder = $templateFinder;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getDataLayerSourceObjects()
    {
        return \Zend_Json_Encoder::encode($this->collectors->collect(), true);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getDataLayerTemplates()
    {
        $templates = $this->templateFinder->findByLayoutHandlers();

        return \Zend_Json_Encoder::encode($templates, true);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBreadcrumbs()
    {
        $pageInformation = ['pageCategory' => 'Home', 'pageSubCategory' => 'Not available'];
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
            $crumbs = $breadcrumbsBlock->getCrumbs();
            if (!$crumbs) {
                $crumbs = $this->catalogHelper->getBreadcrumbPath();
            }
            if ($crumbs) {
                $crumbs = array_values($crumbs);
                $countCrumbs = count($crumbs);
                if ($countCrumbs > 2) {
                    $arrayCrumb = array_slice($crumbs, $countCrumbs - 2, 2);
                    if (!empty($arrayCrumb[0]['label']) && !empty($arrayCrumb[1]['label'])) {
                        $pageCategory = $arrayCrumb[0]['label'];
                        $pageSubCategory = $arrayCrumb[1]['label'];
                        if (gettype($pageCategory) == 'object') {
                            $pageCategory = $pageCategory->getText();
                        }
                        if (gettype($pageSubCategory) == 'object') {
                            $pageSubCategory = $pageSubCategory->getText();
                        }
                        $pageInformation['pageCategory'] = $pageCategory;
                        $pageInformation['pageSubCategory'] = $pageSubCategory;
                    }
                } else {
                    if (!empty(end($crumbs)['label'])) {
                        $pageCategory = end($crumbs)['label'];
                        if (gettype(end($crumbs)['label']) == 'object') {
                            $pageCategory = end($crumbs)['label']->getText();
                        }
                        $pageInformation['pageCategory'] = $pageCategory;
                    }
                }
            }
        }
        return \Zend_Json_Encoder::encode($pageInformation, true);
    }
}

<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 20 2020
 * Time: 2:57 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Block;

class Navigation extends \Magento\LayeredNavigationStaging\Block\Navigation
{
    /**
     * @var \Magento\Cms\Model\PageRepository
     */
    protected $pageRepository;

    /**
     * Navigation constructor.
     *
     * @param \Magento\Cms\Model\PageRepository                      $pageRepository
     * @param \Magento\Framework\View\Element\Template\Context       $context
     * @param \Magento\Catalog\Model\Layer\Resolver                  $layerResolver
     * @param \Magento\Catalog\Model\Layer\FilterList                $filterList
     * @param \Magento\Staging\Model\VersionManager                  $versionManager
     * @param \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag
     * @param array                                                  $data
     */
    public function __construct(
        \Magento\Cms\Model\PageRepository $pageRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Model\Layer\FilterList $filterList,
        \Magento\Staging\Model\VersionManager $versionManager,
        \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag,
        array $data = []
    ) {
        parent::__construct($context, $layerResolver, $filterList, $visibilityFlag, $versionManager, $data);
        $this->pageRepository = $pageRepository;
    }

    /**
     * @return bool
     */
    public function isShowLandingPage()
    {
        $rootCategoryId = $this->_storeManager->getGroup()->getRootCategoryId();

        return $this->getLayer()->getCurrentCategory()->getParentId() === $rootCategoryId;
    }

    /**
     * @return string
     */
    public function getLandingPageUrl()
    {
        $id = $this->getLayer()->getCurrentCategory()->getData('trans_landing_page');
        try {
            $page = $this->pageRepository->getById($id);

            return $this->getUrl($page->getIdentifier());
        } catch (\Exception $e) {
            return '';
        }
    }
}

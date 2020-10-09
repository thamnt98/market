<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Theme
 *
 * Date: April, 08 2020
 * Time: 6:48 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Theme\Plugin\Model\Amasty\Layer;

class SwatchRenderer
{
    /**
     * @var \SM\Catalog\ViewModel\SubCategories
     */
    protected $viewModel;

    /**
     * Item constructor.
     *
     * @param \SM\Catalog\ViewModel\SubCategories $viewModel
     */
    public function __construct(
        \SM\Catalog\ViewModel\SubCategories $viewModel
    ) {
        $this->viewModel = $viewModel;
    }

    /**
     * Update filter url on landing page category.
     *
     * @param \Amasty\Shopby\Block\Navigation\SwatchRenderer $subject
     * @param                                                $result
     *
     * @return string
     */
    public function afterGetSwatchData(\Amasty\Shopby\Block\Navigation\SwatchRenderer $subject, $result)
    {
        $categoryLanding = $this->viewModel->getSubCategories();
        if (!empty($categoryLanding) && !empty($result['options'])) {
            foreach ($result['options'] as &$item) {
                if (empty($item['link'])) {
                    continue;
                }

                $params = explode('?', $item['link']);
                $item['link'] = $categoryLanding->url . '?' . ($params[1] ?? '');
            }
        }

        return $result;
    }
}

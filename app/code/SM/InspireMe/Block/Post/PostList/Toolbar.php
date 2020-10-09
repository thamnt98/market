<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Post\PostList;

use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Blog\Model\Config;
use Mirasvit\Blog\Model\Post\PostList\Toolbar as ToolbarModel;

/**
 * Class Toolbar
 * @package SM\InspireMe\Block\Post\PostList
 */
class Toolbar extends \Mirasvit\Blog\Block\Post\PostList\Toolbar
{
    /**
     * @var \SM\InspireMe\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var int
     */
    protected $pagingConfig = 21;

    /**
     * Toolbar constructor.
     * @param Context $context
     * @param Session $session
     * @param ToolbarModel $toolbarModel
     * @param EncoderInterface $urlEncoder
     * @param Config $config
     * @param Registry $registry
     * @param \SM\InspireMe\Helper\Data $dataHelper
     */
    public function __construct(
        Context $context,
        Session $session,
        ToolbarModel $toolbarModel,
        EncoderInterface $urlEncoder,
        Config $config,
        Registry $registry,
        \SM\InspireMe\Helper\Data $dataHelper
    ) {
        parent::__construct($context, $session, $toolbarModel, $urlEncoder, $config, $registry);
        $this->dataHelper = $dataHelper;
        $this->pagingConfig = $dataHelper->getPagingConfig();
    }

    /**
     * @return array
     */
    public function getAvailableLimit()
    {
        return [$this->pagingConfig => $this->pagingConfig];
    }

    /**
     * @return int|string
     */
    public function getDefaultPerPageValue()
    {
        return $this->pagingConfig;
    }

    /**
     * @return $this|\Mirasvit\Blog\Block\Post\PostList\Toolbar
     */
    private function loadAvailableOrders()
    {
        if ($this->availableOrder === null) {
            $this->availableOrder = [
                'desc' => __('Recent'),
                'asc' => __('Previous'),
            ];
        }

        return $this;
    }

    /**
     * Set default Order field.
     *
     * @param string $field
     *
     * @return \Mirasvit\Blog\Block\Post\PostList\Toolbar
     */
    public function setDefaultOrder($field)
    {
        $this->loadAvailableOrders();
        if (isset($this->availableOrder[$field])) {
            $this->orderField = $field;
        }

        return $this;
    }

    /**
     * Retrieve available Order fields list.
     * @return array
     */
    public function getAvailableOrders()
    {
        $this->loadAvailableOrders();
        return $this->availableOrder;
    }

    /**
     * Get grit products sort order field.
     * @return string
     */
    public function getCurrentOrder()
    {
        return \Mirasvit\Blog\Api\Data\PostInterface::CREATED_AT;
    }

    /**
     * Retrieve current direction.
     * @return string
     */
    public function getCurrentDirection()
    {
        $dir = $this->_getData('blog_current_direction');
        if ($dir) {
            return $dir;
        }

        $directions = ['asc', 'desc'];
        $dir        = strtolower($this->toolbarModel->getDirection());
        if (!$dir || !in_array($dir, $directions)) {
            $dir = $this->direction;
        }

        if ($dir != $this->direction) {
            $this->memorizeParam('sort_direction', $dir);
        }

        $this->setData('blog_current_direction', $dir);

        return $dir;
    }

    /**
     * @param string $dir
     * @return bool
     */
    public function isDirectionCurrent($dir)
    {
        return $dir == $this->getCurrentDirection();
    }

    /**
     * Retrieve widget options in json format
     *
     * @param array $customOptions Optional parameter for passing custom selectors from template
     * @return string
     */
    public function getWidgetOptionsJson(array $customOptions = [])
    {
        $options = [
            'direction' => ToolbarModel::DIRECTION_PARAM_NAME,
            'url'       => $this->getPagerUrl()
        ];
        $options = array_replace_recursive($options, $customOptions);
        return json_encode(['sortInspirePost' => $options]);
    }
}

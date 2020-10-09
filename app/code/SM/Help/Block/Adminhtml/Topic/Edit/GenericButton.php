<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Block\Adminhtml\Topic\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class GenericButton
 * @package SM\Help\Block\Adminhtml\Topic\Edit
 */
class GenericButton implements ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\View\Element\UiComponent\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * GenericButton constructor.
     * @param \Magento\Framework\View\Element\UiComponent\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\Context $context,
        \Magento\Framework\Registry $registry
    ) {

        $this->context = $context;
        $this->registry = $registry;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $param
     * @return string
     */
    public function getUrl($route = '', $param = [])
    {
        return $this->context->getUrl($route, $param);
    }

    /**
     * Get topic
     *
     * @return \SM\Help\Api\Data\TopicInterface
     */
    public function getTopic()
    {
        return $this->registry->registry('sm_help_current_topic');
    }

    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return [];
    }
}

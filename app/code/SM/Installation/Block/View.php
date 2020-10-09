<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Installation
 *
 * Date: May, 15 2020
 * Time: 3:35 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Installation\Block;

class View extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'view.phtml';

    /**
     * @var \SM\Installation\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $encoder;

    /**
     * @var array
     */
    protected $installationData = [];

    /**
     * View constructor.
     *
     * @param \Magento\Framework\Json\EncoderInterface $encoder
     * @param \SM\Installation\Helper\Data             $helper
     * @param \Magento\Catalog\Block\Product\Context   $context
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Framework\Json\EncoderInterface $encoder,
        \SM\Installation\Helper\Data $helper,
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->encoder = $encoder;
    }

    /**
     * Get tooltip.
     *
     * @return string
     */
    public function getTooltip()
    {
        return $this->helper->getTooltip();
    }

    public function getJsonData()
    {
        return $this->encoder->encode($this->getInstallationData());
    }

    /**
     * @return array
     */
    public function getInstallationData()
    {
        return $this->installationData;
    }

    /**
     * @param array|\SM\Installation\Model\InstallationService $installationData
     *
     * @return View
     */
    public function setInstallationData($installationData)
    {
        if ($installationData instanceof \SM\Installation\Model\InstallationService) {
            $installationData = $installationData->getData();
        }

        $this->installationData = $installationData;

        return $this;
    }
}

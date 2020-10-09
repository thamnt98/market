<?php

namespace SM\MobileApi\Helper;

/**
 * Class Common
 * @package SM\MobileApi\Helper
 */
class Common extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $_productMetadata;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface
    ) {
        $this->_productMetadata = $productMetadataInterface;
        parent::__construct($context);
    }

    /**
     * Get magento version and convert to int value
     */
    public function getMagentoVersion()
    {
        return $this->_productMetadata->getVersion();
    }
}

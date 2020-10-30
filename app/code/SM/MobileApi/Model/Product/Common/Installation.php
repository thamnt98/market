<?php

namespace SM\MobileApi\Model\Product\Common;

class Installation
{
    /**
     * @var \SM\MobileApi\Model\Data\ProductInstallation\InstallationFactory
     */
    public $installationFactory;

    /**
     * @var \SM\Installation\Helper\Data
     */
    public $helperInstallation;

    /**
     * Installation constructor.
     * @param \SM\MobileApi\Model\Data\ProductInstallation\InstallationFactory $installationFactory
     * @param \SM\Installation\Helper\Data $helperInstallation
     */
    public function __construct(
        \SM\MobileApi\Model\Data\ProductInstallation\InstallationFactory $installationFactory,
        \SM\Installation\Helper\Data $helperInstallation
    ) {
        $this->installationFactory = $installationFactory;
        $this->helperInstallation = $helperInstallation;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isEnableTooltip($product)
    {
        $isEnabled = $this->helperInstallation->isEnabled();
        if ($isEnabled && $product->getData(\SM\Installation\Helper\Data::PRODUCT_ATTRIBUTE)) {
            return true;
        }

        return false;
    }

    /**
     * Get installation of product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \SM\MobileApi\Model\Data\ProductInstallation\Installation
     */
    public function getInstallationTooltip($product)
    {
        $tooltipStatus = $this->isEnableTooltip($product) ? 1 : 0;

        $installation = $this->installationFactory->create();
        $installation->setStatus($tooltipStatus);
        $installation->setTooltip(__($this->helperInstallation->getTooltip()));
        return $installation;
    }

    /**
     * @return \Magento\Framework\Phrase|null
     */
    public function getTooltipMessage()
    {
        $isEnabled = $this->helperInstallation->isEnabled();
        $tooltipMessage = $this->helperInstallation->getTooltip();

        if ($isEnabled) {
            return __($tooltipMessage);
        }

        return null;
    }
}

<?php

/**
 * @category  SM
 * @package   SM_Label
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Label\Plugin\Catalog\Product\View;

/**
 * Class CustomLabel
 * @package SM\Label\Plugin\Catalog\Product\View
 */
class CustomLabel
{
    /**
     * @var array
     */
    private $allowedNames = [
        'custom.product.label.pdp'
    ];

    /**
     * @var \SM\Label\Model\LabelViewer
     */
    private $helper;

    /**
     * CustomLabel constructor.
     * @param \SM\Label\Model\LabelViewer $helper
     */
    public function __construct(
        \SM\Label\Model\LabelViewer $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(
        $subject,
        $result
    ) {
        $product = $subject->getProduct();
        $name = $subject->getNameInLayout();

        if ($product
            && in_array($name, $this->getAllowedNames())
            && !$subject->getAmlabelObserved()
        ) {
            $subject->setAmlabelObserved(true);
            $result .= $this->helper->renderProductLabel($product, 'product');
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedNames()
    {
        return $this->allowedNames;
    }
}

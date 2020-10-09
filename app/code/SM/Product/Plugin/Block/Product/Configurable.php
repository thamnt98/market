<?php

/**
 * @category  SM
 * @package   SM_Product
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Product\Plugin\Block\Product;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as TypeConfigurable;
use Magento\Framework\Json\Decoder;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\UrlInterface;
use Amasty\Label\Helper\Config;

class Configurable
{
    /**
     * @var Decoder
     */
    private $jsonDecoder;

    /**
     * @var EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Config
     */
    private $helper;

    public function __construct(
        Decoder $jsonDecoder,
        EncoderInterface $jsonEncoder,
        UrlInterface $urlBuilder,
        Config $helper
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
    }

    /**
     * @param TypeConfigurable $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundGetAllowProducts(TypeConfigurable $subject, callable $proceed)
    {
        $products = [];
        $allProducts = $subject->getProduct()->getTypeInstance()->getUsedProducts($subject->getProduct(), null);
        /** @var $product \Magento\Catalog\Model\Product */
        foreach ($allProducts as $product) {
            $products[] = $product;
        }
        $subject->setAllowProducts($products);

        return $subject->getData('allow_products');
    }

    /**
     * @param TypeConfigurable $subject
     * @param $result
     * @return string
     */
    public function afterGetJsonConfig(
        TypeConfigurable $subject,
        $result
    ) {
        $result = $this->jsonDecoder->decode($result);

        $result['priceProductStatus'] = $this->getPriceStatus($subject);

        return $this->jsonEncoder->encode($result);
    }

    /**
     * Collect price Status
     *
     * @param $subject
     * @return array
     */
    private function getPriceStatus($subject)
    {
        $priceStatus = [];
        foreach ($subject->getAllowProducts() as $product) {

            $priceStatus[$product->getId()] =
                [
                    'priceStatus' => $product->getStatus(),
                ];
        }

        return $priceStatus;
    }
}

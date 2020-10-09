<?php

namespace SM\MobileApi\Block\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use SM\MobileApi\Model\Product\Image;

class Image360 extends Template
{
    protected $productImage;

    /**
     * Image360 constructor.
     * @param Template\Context $context
     * @param Image $image
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Image $image,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productImage = $image;
    }

    public function get360Image()
    {
        $productId = $this->getData('product_id');
        $image360 = [];
        if ($productId) {
            try {
                $image360 = $this->productImage->getImage360($productId);
            } catch (NoSuchEntityException $e) {
                return $image360;
            }
        }

        return $image360;
    }
}

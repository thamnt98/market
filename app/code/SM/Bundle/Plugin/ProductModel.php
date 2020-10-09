<?php
/**
 * class ProductModel
 * @package SM\Bundle\Plugin
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Dung Nguyen My <dungnm@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Bundle\Plugin;

class ProductModel
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Bundle\Model\ResourceModel\Option\CollectionFactory
     */
    private $optionCollectionFactory;
    /**
     * @var ProductOptionRepositoryInterface
     */
    private $productOptionRepo;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * ProductModel constructor.
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Bundle\Model\ResourceModel\Option\CollectionFactory $optionCollectionFactory
     * @param \Magento\Bundle\Api\ProductOptionRepositoryInterface $productOptionRepo
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Bundle\Model\ResourceModel\Option\CollectionFactory $optionCollectionFactory,
        \Magento\Bundle\Api\ProductOptionRepositoryInterface $productOptionRepo,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->productRepository = $productRepository;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->productOptionRepo = $productOptionRepo;
        $this->request = $request;
    }

    public function beforeSave($subject, $object)
    {
        $options = $this->optionCollectionFactory->create();
        $options->setIdFilter($object->getOptionId());
        $existingOption = $options->getFirstItem();

        if ($existingOption->getId()) {
            $productIdMinPrice = $this->getProductIdMinPrice($object->getOptionId());
            if ($productIdMinPrice == $object->getProductId() || $productIdMinPrice == 0) {
                $object->setIsDefault("1");
            } else {
                $object->setIsDefault("0");
            }
        }
    }

    public function getProductIdMinPrice($optionId)
    {
        $minPrice = 0;
        $productIdMinPrice = 0;
        $options = ($this->request->getParam('bundle_options', false) && is_array($this->request->getParam('bundle_options'))) ? $this->request->getParam('bundle_options')['bundle_options'] : [];
        foreach ($options as $option) {
            if ($option['option_id'] == $optionId) {
                foreach ($option['bundle_selections'] as $key => $selection) {
                    $prodItem = $this->productRepository->getById($selection['product_id']);
                    $prodItemPrice = $prodItem->getFinalPrice() * (int)$selection['selection_qty'];
                    if ($key == 0) {
                        $productIdMinPrice = $prodItem->getEntityId();
                        $minPrice = $prodItemPrice;
                    } elseif ($minPrice > $prodItemPrice) {
                        $productIdMinPrice = $prodItem->getEntityId();
                        $minPrice = $prodItemPrice;
                    }
                }
            }
        }

        return $productIdMinPrice;
    }
}

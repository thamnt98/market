<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Block
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Block;

use Magento\Framework\View\Element\Template;
use SM\DigitalProduct\Api\Data\CategoryInterface;
use SM\DigitalProduct\Api\Data\C1CategoryDataInterface;
use SM\DigitalProduct\Api\Data\OperatorDataInterface;
use SM\DigitalProduct\Helper\Product\Data;

/**
 * Class AbstractProductList
 * @package SM\DigitalProduct\Block
 */
abstract class AbstractProductList extends Template
{
    /**
     * @var C1CategoryDataInterface[]
     */
    private $c1Categories;

    /**
     * @var CategoryInterface
     */
    private $category;

    /**
     * @var OperatorDataInterface
     */
    private $operator;

    /**
     * @var Data
     */
    protected $productHelper;

    /**
     * ProductList constructor.
     * @param Template\Context $context
     * @param Data $productHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $productHelper,
        array $data = []
    ) {
        $this->productHelper = $productHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return C1CategoryDataInterface[]
     */
    public function getC1Categories()
    {
        return $this->c1Categories;
    }

    /**
     * @param C1CategoryDataInterface[] $value
     * @return $this
     */
    public function setC1Categories($value)
    {
        $this->c1Categories = $value;
        return $this;
    }

    /**
     * @param float $value
     * @return float|string
     */
    public function priceFormat($value)
    {
        return $this->productHelper->currencyFormat($value);
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return OperatorDataInterface
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param OperatorDataInterface $operator
     * @return $this
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
        return $this;
    }
}

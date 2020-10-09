<?php

namespace SM\MobileApi\Api\Data\Catalog;

/**
 * Interface for storing products toolbar information
 */
interface ProductToolbarInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const CURRENT_PAGE_NUM = 'current_page_num';
    const LAST_PAGE_NUM = 'last_page_num';
    const CURRENT_LIMIT = 'current_limit';
    const CURRENT_ORDER = 'current_order';
    const CURRENT_DIRECTION = 'current_direction';
    const AVAILABLE_ORDERS = 'available_orders';
    const PRODUCT_TOTAL = 'product_total';

    /**
     * Get current page
     *
     * @return int
     */
    public function getCurrentPageNum();

    /**
     * @param int $data
     *
     * @return $this
     */
    public function setCurrentPageNum($data);

    /**
     * Get last page
     *
     * @return int
     */
    public function getLastPageNum();

    /**
     * @param int $data
     *
     * @return $this
     */
    public function setLastPageNum($data);

    /**
     * Get current limit
     *
     * @return int
     */
    public function getCurrentLimit();

    /**
     * @param int $data
     *
     * @return $this
     */
    public function setCurrentLimit($data);

    /**
     * Get current order by
     *
     * @return string
     */
    public function getCurrentOrder();

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setCurrentOrder($data);

    /**
     * Get current order direction
     *
     * @return string
     */
    public function getCurrentDirection();

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setCurrentDirection($data);

    /**
     * @return integer
     */
    public function getProductTotal();

    /**
     * @param integer $total
     * @return $this
     */
    public function setProductTotal($total);

    /**
     * Get available orders
     *
     * @return \SM\MobileApi\Api\Data\Catalog\ProductToolbarOrderInterface[]
     */
    public function getAvailableOrders();

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setAvailableOrders($data);
}

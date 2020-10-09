<?php
/**
 * SM\TobaccoAlcoholProduct\Plugin\Catalog\Model\ResourceModel\Category
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\TobaccoAlcoholProduct\Plugin\Catalog\Model\ResourceModel\Category;

use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * @package SM\TobaccoAlcoholProduct\Plugin\Catalog\Model\ResourceModel\Category
 */
class Collection
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Collection constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $subject
     */
    public function beforeLoad($subject) {
        try {
            $subject->addAttributeToFilter(
                [
                    ["attribute" => "is_tobacco", "null" => true],
                    ["attribute" => "is_tobacco", "eq" => 0]
                ]
            );
        } catch (LocalizedException $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}

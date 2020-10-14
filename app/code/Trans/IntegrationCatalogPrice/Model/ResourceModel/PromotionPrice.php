<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime as LibDateTime;
use Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterface;

/**
 * Class Reservation
 */
class PromotionPrice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var LibDateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * Construct
     *
     * @param Context $context
     * @param DateTime $date
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        LibDateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->date     = $date;
        $this->timezone = $timezone;

        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(PromotionPriceInterface::TABLE_NAME, PromotionPriceInterface::ID);
    }

    /**
     * save updated at
     * @SuppressWarnings(PHPMD)
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            if (!$object->hasCreatedAt()) {
                $object->setCreatedAt($this->timezone->date());
            }
        }

        $object->setUpdatedAt($this->timezone->date());

        return parent::_beforeSave($object);
    }
}

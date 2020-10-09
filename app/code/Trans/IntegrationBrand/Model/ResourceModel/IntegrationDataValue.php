<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationBrand\Model\ResourceModel;

use Magento\Framework\Stdlib\DateTime\DateTime as LibDateTime;
use Magento\Framework\Model\AbstractModel;
use Trans\IntegrationBrand\Api\Data\IntegrationDataValueInterface as Interfaces;

/**
 * Class Reservation
 */
class IntegrationDataValue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->date = $date;
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
        $this->_init(Interfaces::TABLE_NAME, Interfaces::ID);
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
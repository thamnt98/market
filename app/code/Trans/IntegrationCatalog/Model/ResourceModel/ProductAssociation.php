<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Ilma Dinnia Alghani <ilma.dinnia@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime as LibDateTime;
use Trans\IntegrationCatalog\Api\Data\ProductAssociationInterface;

/**
 * Class Reservation
 */
class ProductAssociation extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init(ProductAssociationInterface::TABLE_NAME, ProductAssociationInterface::ID);
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

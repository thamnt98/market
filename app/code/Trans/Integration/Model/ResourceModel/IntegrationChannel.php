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
namespace Trans\Integration\Model\ResourceModel;

use Magento\Framework\Stdlib\DateTime\DateTime as LibDateTime;
use Magento\Framework\Model\AbstractModel;
use Trans\Integration\Api\Data\IntegrationChannelInterface;

/**
 * Class Reservation
 */
class IntegrationChannel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * Construct
     *
     * @param Context $context
     * @param DateTime $date
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        LibDateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->date = $date;
        $this->timezone = $timezone;
        $this->authSession = $authSession;
    
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
        $this->_init(IntegrationChannelInterface::TABLE_NAME, IntegrationChannelInterface::ID);
    }

    /**
     * get current admin user
     * 
     * @return \Magento\User\Api\Data\UserInterface
     */
    private function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    /**
     * save updated at
     * @SuppressWarnings(PHPMD)
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $user = $this->getCurrentUser();

        if ($object->isObjectNew()) {
            if (!$object->hasCreatedAt()) {
                $object->setCreatedAt($this->timezone->date());
                if($user) {
                    $object->setCreatedBy($user->getId());
                }
            }
        }

        $object->setUpdatedAt($this->timezone->date());
        if($user) {
            $object->setUpdatedBy($user->getId());
        }

        return parent::_beforeSave($object);
    }
}
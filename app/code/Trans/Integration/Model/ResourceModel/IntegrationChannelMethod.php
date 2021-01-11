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
use Trans\Integration\Api\Data\IntegrationChannelMethodInterface;

/**
 * Class Reservation
 */
class IntegrationChannelMethod extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(        
        LibDateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        $this->date = $date;
        $this->timezone = $timezone;
        $this->authSession = $authSession;
    
        parent::__construct($context, $connectionName);
    }
    
    /**
     * Initialize resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(IntegrationChannelMethodInterface::TABLE_NAME, IntegrationChannelMethodInterface::ID);
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
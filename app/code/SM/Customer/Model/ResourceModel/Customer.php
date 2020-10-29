<?php

declare(strict_types=1);

namespace SM\Customer\Model\ResourceModel;

use Magento\Customer\Model\AccountConfirmation;
use Magento\Customer\Model\Metadata\CustomerMetadata;
use Magento\Customer\Model\ResourceModel\Customer as BaseResourceModel;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Validator\Factory;
use Magento\Store\Model\StoreManagerInterface;
use SM\Customer\Helper\Config;

class Customer extends BaseResourceModel
{

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * Customer constructor.
     * @param Context $context
     * @param Snapshot $entitySnapshot
     * @param RelationComposite $entityRelationComposite
     * @param ScopeConfigInterface $scopeConfig
     * @param Factory $validatorFactory
     * @param DateTime $dateTime
     * @param StoreManagerInterface $storeManager
     * @param EavConfig $eavConfig
     * @param array $data
     * @param AccountConfirmation|null $accountConfirmation
     */
    public function __construct(
        Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        ScopeConfigInterface $scopeConfig,
        Factory $validatorFactory,
        DateTime $dateTime,
        StoreManagerInterface $storeManager,
        EavConfig $eavConfig,
        $data = [],
        AccountConfirmation $accountConfirmation = null
    ) {
        parent::__construct(
            $context,
            $entitySnapshot,
            $entityRelationComposite,
            $scopeConfig,
            $validatorFactory,
            $dateTime,
            $storeManager,
            $data,
            $accountConfirmation
        );

        $this->eavConfig = $eavConfig;
    }

    /**
     * @param string $phoneNumber
     * @return null|int
     * @throws LocalizedException
     */
    public function getCustomerIdByPhoneNumber(string $phoneNumber): ?int
    {
        $attribute = $this->eavConfig->getAttribute(
            CustomerMetadata::ENTITY_TYPE_CUSTOMER,
            Config::PHONE_ATTRIBUTE_CODE
        );

        $connection = $this->getConnection();
        $query = $connection->select()
            ->from([$connection->getTableName($attribute->getBackendTable())])
            ->where('attribute_id = ?', $attribute->getAttributeId())
            ->where('value = ?', $phoneNumber);

        $result = $connection->fetchRow($query);

        return $result ? (int) $result['entity_id'] : null;
    }

    /**
     * @param $telephone
     * @return bool
     */
    public function checkTelephoneIsVerified($telephone)
    {
        $connection = $this->getConnection();
        $table = "sms_verification";
        $query = $connection->select()
            ->from(
                $connection->getTableName($table),
                'is_verified'
            )->where('phone_number = ?', $telephone);

        return (bool) $connection->fetchOne($query) ?? false;
    }
}

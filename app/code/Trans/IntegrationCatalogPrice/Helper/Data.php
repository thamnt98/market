<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationCatalogPrice\Helper;

/**
 * Class Config
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Staging\Api\UpdateRepositoryInterface
     */
    protected $updateRepository;

    /**
     * @var \Magento\Staging\Api\Data\UpdateInterfaceFactory
     */
    protected $updateInterfaceFactory;

    /**
     * @var \Magento\Staging\Model\UpdateFactory
     */
    protected $updateFactory;

    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    protected $ruleRepositoryInterface;

    /**
     * @var \Magento\SalesRule\Api\Data\RuleInterfaceFactory
     */
    protected $ruleInterfaceFactory;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule
     */
    protected $ruleResource;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\FoundFactory
     */
    protected $foundProductRuleFactory;

    /**
     * @var \Amasty\Rules\Model\ResourceModel\Rule
     */
    protected $amastyRuleInterface;

    /**
     * @var \Amasty\Rules\Api\Data\RuleInterfaceFactory
     */
    protected $amastyRuleInterfaceFactory;

    /**
     * @var \Amasty\Promo\Model\ResourceModel\Rule
     */
    protected $amastyPromoInterface;

    /**
     * @var \Amasty\Promo\Api\Data\GiftRuleInterfaceFactory
     */
    protected $amastyPromoInterfaceFactory;

    /**
     * @var \Magento\CatalogStaging\Api\ProductStagingInterface
     */
    protected $productStaging;

    /**
     * @var \Magento\Staging\Model\VersionManagerFactory
     */
    protected $versionManagerFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository
     * @param \Magento\Staging\Api\Data\UpdateInterface $updateInterfaceFactory
     * @param \Magento\Staging\Model\UpdateFactory $updateFactory
     * @param \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepositoryInterface
     * @param \Magento\SalesRule\Api\Data\RuleInterfaceFactory $ruleInterfaceFactory
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param \Magento\SalesRule\Model\ResourceModel\Rule $ruleResource
     * @param \Magento\SalesRule\Model\Rule\Condition\Product\FoundFactory $foundProductRuleFactory
     * @param \Amasty\Rules\Model\ResourceModel\Rule $amastyRuleInterface
     * @param \Amasty\Rules\Api\Data\RuleInterfaceFactory $amastyRuleInterfaceFactory
     * @param \Amasty\Promo\Model\ResourceModel\Rule $amastyPromoInterface
     * @param \Amasty\Promo\Api\Data\GiftRuleInterfaceFactory $amastyPromoInterfaceFactory
     * @param \Magento\CatalogStaging\Api\ProductStagingInterface $productStaging
     * @param \Magento\Staging\Model\VersionManagerFactory $versionManagerFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository,
        \Magento\Staging\Api\Data\updateInterfaceFactory $updateInterfaceFactory,
        \Magento\Staging\Model\UpdateFactory $updateFactory,
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepositoryInterface,
        \Magento\SalesRule\Api\Data\RuleInterfaceFactory $ruleInterfaceFactory,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\SalesRule\Model\ResourceModel\Rule $ruleResource,
        \Magento\SalesRule\Model\Rule\Condition\Product\FoundFactory $foundProductRuleFactory,
        \Amasty\Rules\Model\ResourceModel\Rule $amastyRuleInterface,
        \Amasty\Rules\Api\Data\RuleInterfaceFactory $amastyRuleInterfaceFactory,
        \Amasty\Promo\Model\ResourceModel\Rule $amastyPromoInterface,
        \Amasty\Promo\Api\Data\GiftRuleInterfaceFactory $amastyPromoInterfaceFactory,
        \Magento\CatalogStaging\Api\ProductStagingInterface $productStaging,
        \Magento\Staging\Model\VersionManagerFactory $versionManagerFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->updateRepository            = $updateRepository;
        $this->updateInterfaceFactory      = $updateInterfaceFactory;
        $this->updateFactory               = $updateFactory;
        $this->ruleRepositoryInterface     = $ruleRepositoryInterface;
        $this->ruleInterfaceFactory        = $ruleInterfaceFactory;
        $this->ruleFactory                 = $ruleFactory;
        $this->ruleResource                = $ruleResource;
        $this->foundProductRuleFactory     = $foundProductRuleFactory;
        $this->amastyRuleInterface         = $amastyRuleInterface;
        $this->amastyRuleInterfaceFactory  = $amastyRuleInterfaceFactory;
        $this->amastyPromoInterface        = $amastyPromoInterface;
        $this->amastyPromoInterfaceFactory = $amastyPromoInterfaceFactory;
        $this->productStaging              = $productStaging;
        $this->versionManager              = $versionManagerFactory->create();
        $this->timezone                    = $timezone;
        $this->date                        = $date;

        parent::__construct($context);
    }

    /**
     * Get UpdateRepositoryInterface
     *
     * @return \Magento\Staging\Api\UpdateRepositoryInterface
     */
    public function getUpdateRepositoryInterface()
    {
        return $this->updateRepository;
    }

    /**
     * Get UpdateInterfaceFactory
     *
     * @return \Magento\Staging\Api\Data\UpdateInterface
     */
    public function getUpdateInterfaceFactory()
    {
        return $this->updateInterfaceFactory;
    }

    /**
     * Get UpdateFactory
     *
     * @return \Magento\Staging\Model\UpdateFactory
     */
    public function getUpdateFactory()
    {
        return $this->updateFactory;
    }

    /**
     * Get RuleRepositoryInterface
     *
     * @return \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    public function getRuleRepositoryInterface()
    {
        return $this->ruleRepositoryInterface;
    }

    /**
     * Get RuleInterfaceFactory
     *
     * @return \Magento\SalesRule\Api\Data\RuleInterfaceFactory
     */
    public function getRuleInterfaceFactory()
    {
        return $this->ruleInterfaceFactory;
    }

    /**
     * Get RuleFactory
     *
     * @return \Magento\SalesRule\Model\RuleFactory
     */
    public function getRuleFactory()
    {
        return $this->ruleFactory;
    }

    /**
     * Get Rule
     *
     * @return \Magento\SalesRule\Model\ResourceModel\Rule
     */
    public function getRule()
    {
        return $this->ruleResource;
    }

    /**
     * Get FoundFactory
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\FoundFactory
     */
    public function getFoundFactory()
    {
        return $this->foundProductRuleFactory;
    }

    /**
     * Get amastyRuleInterface
     *
     * @return \Amasty\Rules\Model\ResourceModel\Rule
     */
    public function getAmastyRuleInterface()
    {
        return $this->amastyRuleInterface;
    }

    /**
     * Get amastyRuleInterfaceFactory
     *
     * @return \Amasty\Rules\Api\Data\RuleInterfaceFactory
     */
    public function getAmastyRuleInterfaceFactory()
    {
        return $this->amastyRuleInterfaceFactory;
    }

    /**
     * Get amastyPromoInterface
     *
     * @return \Amasty\Promo\Model\ResourceModel\Rule
     */
    public function getAmastyPromoInterface()
    {
        return $this->amastyPromoInterface;
    }

    /**
     * Get GiftRuleInterfaceFactory
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterfaceFactory
     */
    public function getGiftRuleInterfaceFactory()
    {
        return $this->amastyPromoInterfaceFactory;
    }


    /**
     * Get ProductStagingInterface
     *
     * @return \Magento\CatalogStaging\Api\ProductStagingInterface
     */
    public function getProductStagingInterface()
    {
        return $this->productStaging;
    }

    /**
     * Get VersionManagerFactory
     *
     * @return \Magento\Staging\Model\VersionManagerFactory
     */
    public function getVersionManagerFactory()
    {
        return $this->versionManager;
    }

    /**
     * Get TimezoneInterface
     *
     * @return \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    public function getTimezoneInterface()
    {
        return $this->timezone;
    }

    /**
     * Get DateTime
     *
     * @return \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public function getDateTime()
    {
        return $this->date;
    }
}
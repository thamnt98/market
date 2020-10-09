<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 03 2020
 * Time: 4:26 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Override\MagentoReward\Model\SalesRule;

class SaveHandler extends \Magento\Reward\Model\SalesRule\SaveHandler
{
    /**
     * @var \Magento\Reward\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;
    /**
     * @var \Magento\Reward\Model\ResourceModel\Reward
     */
    protected $rewardResource;

    /**
     * SaveHandler constructor.
     *
     * @param \Magento\Reward\Helper\Data                   $helper
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Reward\Model\ResourceModel\Reward    $rewardResource
     */
    public function __construct(
        \Magento\Reward\Helper\Data $helper,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Reward\Model\ResourceModel\Reward $rewardResource
    ) {
        parent::__construct($helper, $metadataPool, $rewardResource);
        $this->helper = $helper;
        $this->metadataPool = $metadataPool;
        $this->rewardResource = $rewardResource;
    }

    /**
     * @override
     * @param \Magento\SalesRule\Model\Rule|object $entity
     * @param array                                $arguments
     *
     * @return \Magento\SalesRule\Model\Rule|object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        /** @var $entity \Magento\SalesRule\Model\Rule */
        if (!$this->helper->isEnabled()) {
            return $entity;
        }

        $attributes = $entity->getExtensionAttributes() ?: [];
        $metadata = $this->metadataPool->getMetadata(\Magento\SalesRule\Api\Data\RuleInterface::class);
        if ($entity->getData($metadata->getLinkField())) {
            $pointsDelta = $attributes['reward_points_delta'] ?? $entity->getRewardPointsDelta();
            $this->rewardResource->saveRewardSalesrule(
                $entity->getData($metadata->getLinkField()),
                (int)$pointsDelta
            );
        }

        return $entity;
    }
}

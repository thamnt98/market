<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Data;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use SM\InspireMe\Api\Data\LookbookDetailInterface;

/**
 * Class LookbookDetail
 * @package SM\InspireMe\Model\Data
 */
class LookbookDetail extends AbstractModel implements \SM\InspireMe\Api\Data\LookbookDetailInterface
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * LookbookDetail constructor.
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function getImageUrl()
    {
        try {
            return $this->getMediaUrl($this->getData(self::IMAGE));
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getPins()
    {
        return $this->serializer->unserialize($this->getData(self::PINS));
    }

    /**
     * @param string $image
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getMediaUrl($image)
    {
        if (!$image) {
            return false;
        }

        return $this->storeManager->getStore()
                ->getBaseUrl(MagentoUrlInterface::URL_TYPE_MEDIA) . $image;
    }
}

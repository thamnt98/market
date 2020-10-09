<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\TodayDeal\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use SM\TodayDeal\Api\Data\CampaignListingMobileInterface;

/**
 * Class CampaignListingMobile
 * @package SM\TodayDeal\Model
 */
class CampaignListingMobile extends AbstractModel implements \SM\TodayDeal\Api\Data\CampaignListingMobileInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * DealListingMobile constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
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
    public function setId($value)
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setTitle($value)
    {
        return $this->setData(self::TITLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getThumbnailUrl()
    {
        return $this->storeManager->getStore()
                ->getBaseUrl(MagentoUrlInterface::URL_TYPE_MEDIA) . $this->getData('thumbnail_path');
    }

    /**
     * @inheritDoc
     */
    public function setThumbnailUrl($value)
    {
        return $this->setData(self::THUMBNAIL_URL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPeriodDate()
    {
        return $this->convertDate($this->getData('publish_from'), $this->getData('publish_to'));
    }

    /**
     * @param string $from
     * @param string $to
     * @return string
     */
    protected function convertDate($from, $to)
    {
        if (!$from || !$to) {
            return null;
        }

        $from = $this->formatDate($from);
        $to = $this->formatDate($to);

        if ($from[2] === $to[2]) {
            if ($from[1] === $to[1]) {
                return $from[0] . ' - ' . implode(' ', $to);
            }
            return implode(' ', [$from[0], $from[1]]) . ' - ' . implode(' ', $to);
        }
        return implode(' ', $from) . ' - ' . implode(' ', $to);
    }

    /**
     * @param mixed $date
     * @return string[]
     */
    protected function formatDate($date)
    {
        $date = $this->timezone->formatDateTime(
            $date,
            null,
            null,
            null,
            $this->timezone->getConfigTimezone(),
            'd MMM YYYY'
        );
        return explode(' ', $date);
    }

    /**
     * @inheritDoc
     */
    public function setPeriodDate($value)
    {
        return $this->setData(self::PERIOD_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMbShortDescription()
    {
        return $this->getData(self::MB_SHORT_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setMbShortDescription($value)
    {
        return $this->setDataUsingMethod(self::MB_SHORT_DESCRIPTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @inheritDoc
     */
    public function setPosition($value)
    {
        return $this->setData(self::POSITION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPromoId()
    {
        return $this->getData(self::PROMO_ID);
    }

    /**
     * @inheritDoc
     */
    public function getPromoName()
    {
        return $this->getData(self::PROMO_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getPromoCreative()
    {
        return $this->getData(self::PROMO_CREATIVE);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }
}

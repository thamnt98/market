<?php

namespace SM\HeroBanner\Model\Data;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Mageplaza\BannerSlider\Model\Config\Source\Image;
use SM\HeroBanner\Api\Data\BannerInterface;

class Banner extends \Magento\Framework\Model\AbstractExtensibleModel implements BannerInterface
{

    /**
     * @var Image
     */
    protected $imageConfig;

    /**
     * Banner constructor.
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param ExtensionAttributesFactory                                   $extensionFactory
     * @param AttributeValueFactory                                        $customAttributeFactory
     * @param Image                                                        $image
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Image $image,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->imageConfig = $image;
    }

    public function getNewtab()
    {
        return $this->getData('newtab');
    }

    public function setNewtab($data)
    {
        return $this->setData('newtab', $data);
    }

    public function getUrl()
    {
        return $this->getData('url');
    }

    public function setUrl($data)
    {
        return $this->setData('url', $data);
    }

    public function getImage()
    {
        return $this->getData('image');
    }

    public function setImage($data)
    {
        return $this->setData('image', $this->imageConfig->getBaseUrl() . $data);
    }

    public function setName($data)
    {
        return $this->setData('name', $data);
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function setTitle($data)
    {
        return $this->setData('title', $data);
    }

    public function getSubTitle()
    {
        return $this->getData('sub_title');
    }

    public function setSubTitle($data)
    {
        return $this->setData('sub_title', $data);
    }

    public function getContent()
    {
        return $this->getData('content');
    }

    public function setContent($content)
    {
        return $this->setData('content', $content);
    }

    public function getCategoryId()
    {
        return $this->getData('category_id');
    }

    public function setCategoryId($data)
    {
        return $this->setData('category_id', $data);
    }

    public function getPromoId()
    {
        return $this->getData('promo_id');
    }

    public function setPromoId($data)
    {
        return $this->setData('promo_id', $data);
    }

    public function getPromoName()
    {
        return $this->getData('promo_name');
    }

    public function setPromoName($data)
    {
        return $this->setData('promo_name', $data);
    }

    public function getPromoCreative()
    {
        return $this->getData('promo_creative');
    }

    public function setPromoCreative($data)
    {
        return $this->setData('promo_creative', $data);
    }

    /**
     * @inheritDoc
     */
    public function getPromoPosition()
    {
        return $this->getData('promo_position');
    }

    /**
     * @inheritDoc
     */
    public function setPromoPosition($data)
    {
        return $this->setData('promo_position', $data);
    }

    public function getLinkType()
    {
        return $this->getData("link_type");
    }

    public function setLinkType($value)
    {
        return $this->setData("link_type", $value);
    }

    public function getLinkTypeValue()
    {
        return $this->getData("link_type_value");
    }

    public function setLinkTypeValue($value)
    {
        return $this->setData("link_type_value", $value);
    }
}

<?php
namespace Wizkunde\ConfigurableBundle\Model\Config\Source;

class Attribute extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var null|array
     */
    protected $options;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $product
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $product
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->product = $product;
    }

    /**
     * @return array|null
     */
    public function getAllOptions()
    {
        if (null == $this->options) {
            foreach($this->collectionFactory->create()->setEntityTypeFilter($this->product->getTypeId()) as $option)
            {
                $this->options[] = array('value' => $option['attribute_code'], 'label' => $option['frontend_label']);
            }
        }

        return $this->options;
    }
}

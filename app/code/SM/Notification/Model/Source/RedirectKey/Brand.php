<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: October, 15 2020
 * Time: 3:17 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Source\RedirectKey;

class Brand implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var null|array
     */
    protected $options = null;

    /**
     * @var \Amasty\ShopbyBrand\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $repository;

    /**
     * Order constructor.
     *
     * @param \Amasty\ShopbyBrand\Helper\Data                     $helper
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $repository
     */
    public function __construct(
        \Amasty\ShopbyBrand\Helper\Data $helper,
        \Magento\Catalog\Model\Product\Attribute\Repository $repository
    ) {
        $this->helper = $helper;
        $this->repository = $repository;
    }

    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $this->options = [];
            $attributeCode = $this->helper->getBrandAttributeCode();
            try {
                $options = $this->repository->get($attributeCode)->getOptions();
                $aliases = $this->helper->getBrandAliases();
                array_shift($options);

                foreach ($options as $option) {
                    if (isset($aliases[$option->getValue()])) {
                        $this->options[] = [
                            'label' => $option->getLabel(),
                            'value' => $aliases[$option->getValue()],
                        ];
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return $this->options;
    }
}

<?php
/**
 * Class IsActive
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Model\Source;

/**
 * Class IsActive
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class IsActive implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Brand Model
     *
     * @var \Trans\Brand\Model\Brand
     */
    protected $brand;

    /**
     * Constructor
     *
     * @param \Trans\Brand\Model\Brand $brand brandModel
     */
    public function __construct(\Trans\Brand\Model\Brand $brand)
    {
        $this->brand = $brand;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->brand->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $options;
    }
}

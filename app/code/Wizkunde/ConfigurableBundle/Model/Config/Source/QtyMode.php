<?php
namespace Wizkunde\ConfigurableBundle\Model\Config\Source;

class QtyMode extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $options;

    /**
     * Retrieve all options for the source from configuration
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->options === null) {
            $this->options = [
                ['value' => 'normal', 'label' => 'Normal Bundle Behaviour'],
                ['value' => 'grid', 'label' => 'Grid Accumulation'],
                ['value' => 'all', 'label' => 'All Items'],
                ['value' => 'all_without', 'label' => 'Items Without Attribute'],
                ['value' => 'all_with', 'label' => 'Items With Attribute']
            ];
        }

        return $this->options;
    }
}

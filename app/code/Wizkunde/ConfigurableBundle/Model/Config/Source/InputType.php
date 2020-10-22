<?php
namespace Wizkunde\ConfigurableBundle\Model\Config\Source;

class InputType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
                ['value' => 'normal', 'label' => 'Display clickable images, hide actual option input'],
                ['value' => 'both', 'label' => 'Show clickable images with option input below'],
                ['value' => 'input', 'label' => 'Show only option input and hide clickable images']
            ];
        }

        return $this->options;
    }
}

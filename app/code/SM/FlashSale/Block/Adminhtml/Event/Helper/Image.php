<?php

namespace SM\FlashSale\Block\Adminhtml\Event\Helper;

class Image extends \Magento\Framework\Data\Form\Element\Image
{
    /**
     * Get url for image
     *
     * @return string|boolean
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->getForm()->getDataObject()->getImageUrl();
        }
        return $url;
    }
}

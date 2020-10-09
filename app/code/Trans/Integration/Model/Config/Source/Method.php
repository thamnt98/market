<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Integration\Model\Config\Source;

/**
 * Class Method
 */
class Method implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'GET', 'label' => __('GET')], ['value' => 'POST', 'label' => __('POST')], ['value' => 'PUT', 'label' => __('PUT')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['GET' => __('GET'), 'POST' => __('POST'), 'PUT' => __('PUT')];
    }
}

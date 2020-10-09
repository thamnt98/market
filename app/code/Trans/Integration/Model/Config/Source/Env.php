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
 * Class Env
 */
class Env implements \Magento\Framework\Option\ArrayInterface
{
    const PROD = 'production';
    const DEV = 'development';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => self::PROD, 'label' => __('Production')], ['value' => self::DEV, 'label' => __('Development')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::DEV => __('Development'), self::PROD => __('Production')];
    }
}

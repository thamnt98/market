<?php
/**
 * Class RuleSuggestion
 * @package SM\Promotion\Model\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Promotion\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use SM\Promotion\Api\Data\RuleSuggestionInterface;

class RuleSuggestion extends AbstractSimpleObject implements RuleSuggestionInterface
{
    /**
     * @inheritDoc
     */
    public function getRuleDescription()
    {
        return $this->_get(self::RULE_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setRuleDescription($value)
    {
        return $this->setData(self::RULE_DESCRIPTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMobileRedirect()
    {
        return $this->_get(self::MOBILE_REDIRECT);
    }

    /**
     * @inheritDoc
     */
    public function setMobileRedirect($value)
    {
        return $this->setData(self::MOBILE_REDIRECT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMobileArea()
    {
        return $this->_get(self::MOBILE_AREA);
    }

    /**
     * @inheritDoc
     */
    public function setMobileArea($value)
    {
        return $this->setData(self::MOBILE_AREA, $value);
    }

    /**
     * @param string $key
     * @param null $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if ($key === (array)$key) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }
}

<?php
/**
 * Class RuleSuggestionInterface
 * @package SM\Promotion\Api\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Promotion\Api\Data;

interface RuleSuggestionInterface
{
    const RULE_DESCRIPTION = 'description';
    const MOBILE_REDIRECT = 'mobile_redirect';
    const MOBILE_AREA = 'mobile_redirect_area';

    /**
     * @return string
     */
    public function getRuleDescription();

    /**
     * @param string $value
     * @return $this
     */
    public function setRuleDescription($value);

    /**
     * @return string
     */
    public function getMobileRedirect();

    /**
     * @param string $value
     * @return $this
     */
    public function setMobileRedirect($value);

    /**
     * @return int
     */
    public function getMobileArea();

    /**
     * @param int $value
     * @return $this
     */
    public function setMobileArea($value);
}

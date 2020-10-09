<?php
/**
 * interface ResponseInterface
 * @package SM\JiraService\Api\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Dung Nguyen My <dungnm@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\JiraService\Api\Data;

interface ResponseInterface
{
    /**
     * @param boolean $status
     * @return $this
     */
    public function setError($status);

    /**
     * @return boolean
     */
    public function getError();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getMessage();
}

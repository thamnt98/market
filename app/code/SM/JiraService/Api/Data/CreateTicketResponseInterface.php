<?php
/**
 * interface CreateTicketResponseInterface
 * @package SM\JiraService\Api\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Dung Nguyen My <dungnm@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\JiraService\Api\Data;

interface CreateTicketResponseInterface
{
    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $key
     * @return $this
     */
    public function setKey($key);

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param string $url
     * @return $this
     */
    public function setSelfLink($url);

    /**
     * @return string
     */
    public function getSelfLink();

    /**
     * @param boolean $status
     * @return $this
     */
    public function setIsError($status);

    /**
     * @return boolean
     */
    public function getIsError();

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

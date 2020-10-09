<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Api\Data;

/**
 * Interface TopicInterface
 * @package SM\Help\Api\Data
 */
interface TopicInterface
{
    const ID = 'topic_id';

    const NAME        = 'name';
    const STATUS      = 'status';
    const DESCRIPTION = 'description';
    const PATH        = 'path';
    const LEVEL       = 'level';
    const POSITION    = 'position';
    const PARENT_ID   = 'parent_id';
    const CREATED_AT  = 'created_at';
    const URL_KEY     = 'url_key';
    const IMAGE       = 'image';
    const TYPE        = 'type';

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED  = 1;

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $value
     * @return $this
     */
    public function setDescription($value);

    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * @param string $value
     * @return $this
     */
    public function setUrlKey($value);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $value
     * @return $this
     */
    public function setPath($value);

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param int $value
     * @return $this
     */
    public function setLevel($value);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $value
     * @return $this
     */
    public function setPosition($value);

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @param int $value
     * @return $this
     */
    public function setParentId($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getImage();

    /**
     * @return string
     */
    public function getImageUrl();

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);
}

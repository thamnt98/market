<?php

namespace Mirasvit\Blog\Api\Data;

interface CategoryInterface
{
    const ID = 'entity_id';

    const URL_KEY   = 'url_key';
    const PATH      = 'path';
    const LEVEL     = 'level';
    const POSITION  = 'position';
    const PARENT_ID = 'parent_id';

    const NAME             = 'name';
    const CONTENT          = 'content';
    const META_TITLE       = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const META_KEYWORDS    = 'meta_keywords';

    const STATUS = 'status';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);
}

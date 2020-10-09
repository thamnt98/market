<?php
/**
 * Class SearchResults
 * @package SM\Help\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Help\Api\Data;

interface SearchQuestionInterface
{
    const ID          = 'question_id';
    const TITLE       = 'title';
    const URL_KEY     = 'url_key';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getUrlKey();
}

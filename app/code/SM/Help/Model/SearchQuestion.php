<?php
/**
 * Class Search
 * @package SM\Help\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Help\Model;

use Magento\Framework\Model\AbstractModel;
use SM\Help\Api\Data\SearchQuestionInterface;

class SearchQuestion extends AbstractModel implements SearchQuestionInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SM\Help\Model\ResourceModel\Question::class);
    }

    /**
     * Identifier getter
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->_getData($this->_idFieldName);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }
}

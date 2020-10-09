<?php
/**
 * Class CategoryContentRepository
 * @package SM\DigitalProduct\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\DigitalProduct\Model;

class CategoryContentRepository implements \SM\DigitalProduct\Api\CategoryContentRepositoryInterface
{
    /**
     * @var \SM\DigitalProduct\Helper\Category\Content
     */
    private $contentHelper;

    public function __construct(
        \SM\DigitalProduct\Helper\Category\Content $contentHelper
    ) {
        $this->contentHelper = $contentHelper;
    }

    /**
     * @inheritDoc
     */
    public function getCategoryContent($categoryCode)
    {
        return $this->contentHelper->getCategoryContent($categoryCode);
    }
}

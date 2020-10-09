<?php
/**
 * Class Search
 * @package SM\Help\ViewModel
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Help\ViewModel;

use Magento\Framework\Exception\NoSuchEntityException;

class Search implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \SM\Help\Model\Url
     */
    private $helpUrl;

    /**
     * Search constructor.
     * @param \SM\Help\Model\Url $helpUrl
     */
    public function __construct(
        \SM\Help\Model\Url $helpUrl
    ) {
        $this->helpUrl = $helpUrl;
    }

    /**
     * @return int|string
     */
    public function getCurrentStoreCode()
    {
        return $this->helpUrl->getCurrentStoreCode();
    }

    /**
     * @return string
     */
    public function getQuestionBaseUrl()
    {
        return $this->helpUrl->getQuestionBaseUrl();
    }

    /**
     * @return string
     */
    public function getQuestionUrlSuffix()
    {
        return $this->helpUrl->getQuestionUrlSuffix();
    }
}

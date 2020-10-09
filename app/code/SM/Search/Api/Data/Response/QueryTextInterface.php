<?php

namespace SM\Search\Api\Data\Response;

interface QueryTextInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const QUERY_TEXT = 'query_text';

    /**
     * @return string
     */
    public function getQueryText();

    /**
     * @param string $value
     * @return $this
     */
    public function setQueryText($value);
}

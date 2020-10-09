<?php

declare(strict_types=1);

namespace SM\Search\Model\Data\Response;

class QueryText extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\Search\Api\Data\Response\QueryTextInterface
{
    public function getQueryText()
    {
        return $this->getData(self::QUERY_TEXT);
    }

    public function setQueryText($value)
    {
        return $this->setData(self::QUERY_TEXT, $value);
    }
}

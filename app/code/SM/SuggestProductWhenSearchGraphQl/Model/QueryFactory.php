<?php

namespace SM\SuggestProductWhenSearchGraphQl\Model;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\StringUtils as StdlibString;
use Magento\Search\Helper\Data;

/**
 * Class QueryFactory
 * @package SM\SuggestProductWhenSearchGraphQl\Model
 */
class QueryFactory extends \Magento\Search\Model\QueryFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Data
     */
    private $queryHelper;

    /**
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StdlibString $string
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StdlibString $string
    )
    {
        parent::__construct($context, $objectManager, $string, $this->queryHelper);
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $data
     * @return \Magento\Search\Model\Query|mixed|Query
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create(Query::class, $data);
    }
}

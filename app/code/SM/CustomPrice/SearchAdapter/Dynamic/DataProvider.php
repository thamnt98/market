<?php


namespace SM\CustomPrice\SearchAdapter\Dynamic;


use Magento\Customer\Model\Session;
use Magento\Elasticsearch\SearchAdapter\QueryContainer;

class DataProvider extends \Magento\Elasticsearch\SearchAdapter\Dynamic\DataProvider
{
    /**
     * @var \Magento\Elasticsearch\SearchAdapter\ConnectionManager
     * @since 100.1.0
     */
    protected $connectionManager;

    /**
     * @var \Magento\Elasticsearch\Model\Adapter\FieldMapperInterface
     * @since 100.1.0
     */
    protected $fieldMapper;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\Price\Range
     * @since 100.1.0
     */
    protected $range;

    /**
     * @var \Magento\Framework\Search\Dynamic\IntervalFactory
     * @since 100.1.0
     */
    protected $intervalFactory;

    /**
     * @var \Magento\Elasticsearch\Model\Config
     * @deprecated 100.2.0 as this class shouldn't be responsible for query building
     * and should only modify existing query
     * @since 100.1.0
     */
    protected $clientConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     * @deprecated 100.2.0 as this class shouldn't be responsible for query building
     * and should only modify existing query
     * @since 100.1.0
     */
    protected $storeManager;

    /**
     * @var \Magento\Elasticsearch\SearchAdapter\SearchIndexNameResolver
     * @deprecated 100.2.0 as this class shouldn't be responsible for query building
     * and should only modify existing query
     * @since 100.1.0
     */
    protected $searchIndexNameResolver;

    /**
     * @var string
     * @deprecated 100.2.0 as this class shouldn't be responsible for query building
     * and should only modify existing query
     * @since 100.1.0
     */
    protected $indexerId;

    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     * @since 100.1.0
     */
    protected $scopeResolver;

    /**
     * @var QueryContainer
     */
    protected $queryContainer;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;
    /**
     * @var string
     */
    protected $currentAttribute;

    /**
     * @param \Magento\Elasticsearch\SearchAdapter\ConnectionManager       $connectionManager
     * @param \Magento\Elasticsearch\Model\Adapter\FieldMapperInterface    $fieldMapper
     * @param \Magento\Catalog\Model\Layer\Filter\Price\Range              $range
     * @param \Magento\Framework\Search\Dynamic\IntervalFactory            $intervalFactory
     * @param \Magento\Elasticsearch\Model\Config                          $clientConfig
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Elasticsearch\SearchAdapter\SearchIndexNameResolver $searchIndexNameResolver
     * @param string                                                       $indexerId
     * @param \Magento\Framework\App\ScopeResolverInterface                $scopeResolver
     * @param QueryContainer|null                                          $queryContainer
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Elasticsearch\SearchAdapter\ConnectionManager $connectionManager,
        \Magento\Elasticsearch\Model\Adapter\FieldMapperInterface $fieldMapper,
        \Magento\Catalog\Model\Layer\Filter\Price\Range $range,
        \Magento\Framework\Search\Dynamic\IntervalFactory $intervalFactory,
        \Magento\Elasticsearch\Model\Config $clientConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Elasticsearch\SearchAdapter\SearchIndexNameResolver $searchIndexNameResolver,
        $indexerId,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        Session $session,
        QueryContainer $queryContainer = null
    ) {
        parent::__construct($connectionManager, $fieldMapper, $range, $intervalFactory, $clientConfig, $storeManager,
            $searchIndexNameResolver, $indexerId, $scopeResolver, $queryContainer);
        $this->connectionManager       = $connectionManager;
        $this->fieldMapper             = $fieldMapper;
        $this->range                   = $range;
        $this->intervalFactory         = $intervalFactory;
        $this->clientConfig            = $clientConfig;
        $this->storeManager            = $storeManager;
        $this->searchIndexNameResolver = $searchIndexNameResolver;
        $this->indexerId               = $indexerId;
        $this->scopeResolver           = $scopeResolver;
        $this->queryContainer          = $queryContainer;
        $this->customerSession         = $session;
        $this->eavConfig = $eavConfig;
        $this->currentAttribute = 'price';
        if ($session->isLoggedIn()) {
            $this->currentAttribute = $session->getOmniFinalPriceAttributeCode();
            $productAttribute = $this->eavConfig->getAttribute('catalog_product',$this->currentAttribute);
            if (!$productAttribute||!$productAttribute->getAttributeId()) {
                $this->currentAttribute = 'price';
            }

        }
    }


    /**
     * @inheritdoc
     * @since 100.1.0
     */
    public function getAggregations(\Magento\Framework\Search\Dynamic\EntityStorage $entityStorage)
    {
        $aggregations = [
            'count' => 0,
            'max'   => 0,
            'min'   => 0,
            'std'   => 0,
        ];

        $query    = $this->getBasicSearchQuery($entityStorage);
        $fieldName     = $this->fieldMapper->getFieldName($this->currentAttribute);

        $query['body']['aggregations'] = [
            'prices' => [
                'extended_stats' => [
                    'field' => $fieldName,
                ],
            ],
        ];

        $queryResult = $this->connectionManager->getConnection()
                                               ->query($query);

        if (isset($queryResult['aggregations']['prices'])) {
            $aggregations = [
                'count' => $queryResult['aggregations']['prices']['count'],
                'max'   => $queryResult['aggregations']['prices']['max'],
                'min'   => $queryResult['aggregations']['prices']['min'],
                'std'   => $queryResult['aggregations']['prices']['std_deviation'],
            ];
        }

        return $aggregations;
    }


    /**
     * @inheritdoc
     * @since 100.1.0
     */
    public function getAggregation(
        \Magento\Framework\Search\Request\BucketInterface $bucket,
        array $dimensions,
        $range,
        \Magento\Framework\Search\Dynamic\EntityStorage $entityStorage
    ) {
        $result = [];

        $query    = $this->getBasicSearchQuery($entityStorage);
        if ($bucket->getField() == 'price') {
            $fieldName     = $this->fieldMapper->getFieldName($this->currentAttribute);
        } else {
            $fieldName = $this->fieldMapper->getFieldName($bucket->getField());
        }
        $query['body']['aggregations'] = [
            'prices' => [
                'histogram' => [
                    'field'         => $fieldName,
                    'interval'      => (float)$range,
                    'min_doc_count' => 1,
                ],
            ],
        ];
        $queryResult                   = $this->connectionManager->getConnection()
                                                                 ->query($query);
        foreach ($queryResult['aggregations']['prices']['buckets'] as $bucket) {
            $key          = (int)($bucket['key'] / $range + 1);
            $result[$key] = $bucket['doc_count'];
        }

        return $result;
    }


    /**
     * Returns a basic search query which can be used for aggregations calculation
     *
     * The query may be requested from a query container if it has been set
     * or may be build by entity storage and dimensions.
     *
     * Building a query by entity storage is actually deprecated as the query
     * built in this way may cause ElasticSearch's TooManyClauses exception.
     *
     * The code which is responsible for building query in-place should be removed someday,
     * but for now it's a question of backward compatibility as this class may be used somewhere else
     * by extension developers and we can't guarantee that they'll pass a query into constructor.
     *
     * @param \Magento\Framework\Search\Dynamic\EntityStorage $entityStorage
     * @param array                                           $dimensions
     * @return array
     */
    protected function getBasicSearchQuery(
        \Magento\Framework\Search\Dynamic\EntityStorage $entityStorage,
        array $dimensions = []
    ) {
        if (null !== $this->queryContainer) {
            return $this->queryContainer->getQuery();
        }

        $entityIds = $entityStorage->getSource();

        $dimension = current($dimensions);
        $storeId   = false !== $dimension
            ? $this->scopeResolver->getScope($dimension->getValue())->getId()
            : $this->storeManager->getStore()->getId();

        $query = [
            'index' => $this->searchIndexNameResolver->getIndexName($storeId, $this->indexerId),
            'type'  => $this->clientConfig->getEntityType(),
            'body'  => [
                'fields' => [
                    '_id',
                    '_score',
                ],
                'query'  => [
                    'bool' => [
                        'must' => [
                            [
                                'terms' => [
                                    '_id' => $entityIds,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        return $query;
    }
}

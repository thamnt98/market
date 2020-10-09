<?php

declare(strict_types=1);

namespace SM\Search\Plugin\Magento\Elasticsearch6\Model\Client;

use Magento\Elasticsearch6\Model\Client\Elasticsearch as BaseElasticsearch;
use Psr\Log\LoggerInterface;
use SM\Search\Helper\Config;
use SM\Search\Model\Search\Suggestion\Query\Handler as SuggestionQueryHandler;

class Elasticsearch
{
    /**
     * @var SuggestionQueryHandler
     */
    protected $suggestionQueryHandler;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Elasticsearch constructor.
     * @param SuggestionQueryHandler $suggestionQueryHandler
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        SuggestionQueryHandler $suggestionQueryHandler,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->suggestionQueryHandler = $suggestionQueryHandler;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param BaseElasticsearch $subject
     * @param array $query
     * @return array
     */
    public function aroundQuery(BaseElasticsearch $subject, \Closure $proceed, array $query): array
    {
        if ($this->config->isEnableSearchById()
            && !empty($query['body']['query']['bool']['should'][0]['match']['_search']['query'])) {
            $query['body']['query']['bool']['should'][] = [
                'match' => [
                    '_id' => [
                        'query' => $query['body']['query']['bool']['should'][0]['match']['_search']['query'],
                        'boost' => 10,
                    ]
                ]
            ];
        }

        $query = $this->suggestionQueryHandler->handle($query);

        if ($this->config->isEnableDebug()) {
            $this->logger->info('Elasticsearch6-Plugin::beforeQuery()', [
                'isEnableSearchById' => $this->config->isEnableSearchById(),
                'query' => $query
            ]);
        }

        $result = $proceed($query);

        if ($this->config->isEnableDebug()) {
            $this->logger->info('Elasticsearch6-Plugin::afterQuery()', [
                'result' => $result
            ]);
        }

        return $result;
    }
}

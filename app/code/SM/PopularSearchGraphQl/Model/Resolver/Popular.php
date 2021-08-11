<?php

namespace SM\PopularSearchGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\UrlFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory;
use Magento\Framework\View\Element\Template;

/**
 * Class Popular
 * @package SM\PopularSearchGraphQl\Model\Resolver
 */
class Popular extends Template implements ResolverInterface
{
    /**
     * Url factory
     *
     * @var UrlFactory
     */
    protected $_urlFactory;

    /**
     * Query collection factory
     *
     * @var CollectionFactory
     */
    protected $_queryCollectionFactory;

    /**
     * @param Context $context
     * @param CollectionFactory $queryCollectionFactory
     * @param UrlFactory $urlFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $queryCollectionFactory,
        UrlFactory $urlFactory,
        array $data = []
    )
    {
        $this->_queryCollectionFactory = $queryCollectionFactory;
        $this->_urlFactory = $urlFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return \Magento\Framework\GraphQl\Query\Resolver\Value|mixed|void
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $result = [];
        $terms = $this->_queryCollectionFactory->create()->setPopularQueryFilter(
            $this->_storeManager->getStore()->getId())
            ->setPageSize(
                $args['page_size'] ?? 100)
            ->setCurPage($args['current_page'] ?? 0)
            ->load()->getItems();
        if (count($terms)) {
            $termKeys = [];
            foreach ($terms as $term) {
                if (!$term->getPopularity()) {
                    continue;
                }
                $termKeys[] = $term->getQueryText();
            }
            foreach ($termKeys as $termKey) {
                $result[] = [
                    'title' => $termKey,
                    'url' => $this->getSearchUrl($termKey)
                ];
            }
        }
        return $result;
    }

    /**
     * @param \Magento\Framework\DataObject $obj
     * @return string
     */
    public function getSearchUrl($text)
    {
        /** @var $url UrlInterface */
        $url = $this->_urlFactory->create();
        /*
         * url encoding will be done in Url.php http_build_query
         * so no need to explicitly called urlencode for the text
         */
        $url->setQueryParam('q', $text);
        return $url->getUrl('catalogsearch/result');
    }
}

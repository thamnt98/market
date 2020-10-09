<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_InspireMe
 *
 * Date: April, 17 2020
 * Time: 4:15 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\InspireMe\Model\ResourceModel\Post;

class Collection extends \Mirasvit\Blog\Model\ResourceModel\Post\Collection
{
    /**
     * @var \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $postTopicCollFact;

    /**
     * Collection constructor.
     *
     * @param \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $postTopicCollFact
     * @param \Magento\Framework\Data\Collection\EntityFactory              $entityFactory
     * @param \Psr\Log\LoggerInterface                                      $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface  $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                     $eventManager
     * @param \Magento\Eav\Model\Config                                     $eavConfig
     * @param \Magento\Framework\App\ResourceConnection                     $resource
     * @param \Magento\Eav\Model\EntityFactory                              $eavEntityFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper                       $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory                 $universalFactory
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null           $connection
     */
    public function __construct(
        \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $postTopicCollFact,
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $connection
        );
        $this->postTopicCollFact = $postTopicCollFact;
    }

    /**
     * @param string $q
     * @return $this|Collection
     */
    public function addSearchFilter($q)
    {
        $likeExpression = $this->_resourceHelper->addLikeEscape($q, ['position' => 'any']);
        /** @var \Mirasvit\Blog\Model\ResourceModel\Category\Collection $topicColl */

        $topicColl = $this->postTopicCollFact->create();
        try {
            $topicIds = $topicColl->addAttributeToFilter('name', ['like' => $likeExpression])->getAllIds();
            $this->getSelect()
                ->joinLeft(['tp' => 'mst_blog_tag_post'], 'tp.post_id = e.entity_id', [])
                ->joinLeft(['tag' => 'mst_blog_tag'], 'tag.tag_id = tp.tag_id', [])
                ->joinLeft(['topic' => 'mst_blog_category_post'], 'topic.post_id = e.entity_id', []);
            $this->addAttributeToSelect('name', 'inner')
                ->addAttributeToSelect(['content', 'short_content'], 'left');

            $orWhere = [
                "(at_name.value like $likeExpression)",
                "(at_content.value like $likeExpression)",
                "(at_short_content.value like $likeExpression)",
                "(tag.name like $likeExpression)"
            ];
            if (count($topicIds)) {
                $orWhere[] = '(topic.category_id in (' . implode($topicIds) . '))';
            }

            $this->getSelect()->where(implode(' OR ', $orWhere))->group('e.entity_id');
        } catch (\Exception $e) {
        }

        return $this;
    }
}

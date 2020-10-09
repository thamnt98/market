<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Cron\Article;

use Magento\Framework\App\ResourceConnection;

/**
 * Class UpdateMostViewed
 * @package SM\InspireMe\Cron\Article
 */
class UpdateMostViewed
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * UpdateMostViewed constructor.
     * @param ResourceConnection $resourceConnection
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Eav\Model\Config $eavConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Update views_count based on temp_views_count
     */
    public function execute()
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->beginTransaction();
        try {
            //Get views_count eav_attribute_id
            $sql = $connection->select()->from(
                ['ea' => $this->resourceConnection->getTableName('eav_attribute')],
                ['attribute_id']
            )->where(
                'ea.attribute_code = ?',
                'views_count'
            )->where(
                'ea.entity_type_id = ?',
                $this->eavConfig->getEntityType(\Mirasvit\Blog\Model\Post::ENTITY)->getId()
            );
            $viewsCountId = (int)$connection->fetchOne($sql);

            //Get temp_views_count eav_attribute_id
            $sql = $connection->select()->from(
                ['ea' => $this->resourceConnection->getTableName('eav_attribute')],
                ['attribute_id']
            )->where(
                'ea.attribute_code = ?',
                'temp_views_count'
            )->where(
                'ea.entity_type_id = ?',
                $this->eavConfig->getEntityType(\Mirasvit\Blog\Model\Post::ENTITY)->getId()
            );
            $tempViewsCountId = (int)$connection->fetchOne($sql);

            //Get flag_views_changed eav_attribute_id
            $sql = $connection->select()->from(
                ['ea' => $this->resourceConnection->getTableName('eav_attribute')],
                ['attribute_id']
            )->where(
                'ea.attribute_code = ?',
                'flag_views_changed'
            )->where(
                'ea.entity_type_id = ?',
                $this->eavConfig->getEntityType(\Mirasvit\Blog\Model\Post::ENTITY)->getId()
            );
            $flagViewsChanged = (int)$connection->fetchOne($sql);

            $postEavIntTable = $this->resourceConnection->getTableName('mst_blog_post_entity_int');

            $sql = $connection->select()->from(
                ['main_table' => $this->resourceConnection->getTableName('mst_blog_post_entity')],
                ['main_table.entity_id']
            )->joinLeft(
                ['post_eav_int' => $postEavIntTable],
                "main_table.entity_id = post_eav_int.entity_id",
                ['']
            )->where(
                'post_eav_int.attribute_id = ? AND post_eav_int.value = 1',
                $flagViewsChanged
            );

            $postIds = $connection->fetchCol($sql);

            //Update views_count = temp_views_count
            foreach ($postIds as $id) {
                $id = (int)$id;

                $sql = $connection->select()->from(
                    ['post_eav_int' => $postEavIntTable],
                    ['value']
                )->where(
                    "attribute_id = {$tempViewsCountId}
                    AND entity_id = {$id}"
                );
                $tempViewsCountValue = (int)$connection->fetchOne($sql);

                $connection->update(
                    $postEavIntTable,
                    ['value' => $tempViewsCountValue],
                    [
                        'entity_id = ?'    => $id,
                        'attribute_id = ?' => $viewsCountId,
                    ]
                );

                $connection->update(
                    $postEavIntTable,
                    ['value' => 0],
                    [
                        'entity_id = ?'    => $id,
                        'attribute_id = ?' => $flagViewsChanged,
                    ]
                );
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->logger->critical($e->getMessage());
        }
    }
}

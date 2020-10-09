<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Model\ResourceModel;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\ResourceModel\Db\Context;
use SM\Help\Api\Data\TopicInterface;
use Zend_Db_Expr;

/**
 * Class Topic
 * @package SM\Help\Model\ResourceModel
 */
class Topic extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var FilterManager
     */
    protected $filter;

    /**
     * @var \SM\Help\Helper\Topic
     */
    protected $helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Topic constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SM\Help\Helper\Topic $helper
     * @param FilterManager $filter
     * @param Context $context
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Help\Helper\Topic $helper,
        FilterManager $filter,
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->filter = $filter;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sm_help_topic', 'topic_id');
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        return $select->join(
            ['topic_store' => 'sm_help_topic_store'],
                $this->getMainTable() . '.topic_id = topic_store.topic_id',
                ['store_id', 'name', 'status', 'description']
            )
            ->where('store_id = ' . $this->helper->getCurrentStoreId());
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $topic)
    {
        /** @var \SM\Help\Model\Topic $topic */

        parent::_beforeSave($topic);

        if (!$topic->getData(TopicInterface::URL_KEY)) {
            $topic->setData(TopicInterface::URL_KEY, $this->filter->translitUrl($topic->getName()));
        }

        if (!$topic->getData(TopicInterface::PARENT_ID)) {
            $topic->setData(TopicInterface::PARENT_ID, \SM\Help\Model\Topic::TREE_ROOT_ID);
        }

        $topic->setData('main_table.topic_id', $topic->getData('topic_id'));

        if ($topic->isObjectNew()) {
            /** @var \SM\Help\Model\Topic $parent */
            $parent = ObjectManager::getInstance()
                ->create(\SM\Help\Model\Topic::class)
                ->load($topic->getParentId());

            $topic->setPath($parent->getPath());

            if ($topic->getPosition() === null) {
                $topic->setPosition($this->getMaxPosition($topic->getPath()) + 1);
            }

            $path          = explode('/', $topic->getPath());
            $level         = count($path) - ($topic->getId() ? 1 : 0);
            $toUpdateChild = array_diff($path, [$topic->getId()]);

            if (!$topic->hasPosition()) {
                $topic->setPosition($this->getMaxPosition(implode('/', $toUpdateChild)) + 1);
            }

            if (!$topic->hasLevel()) {
                $topic->setLevel($level);
            }

            if (!$topic->getId() && $topic->getPath()) {
                $topic->setPath($topic->getPath() . '/');
            }
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return Topic
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setData('main_table.topic_id', $object->getData('topic_id'));
        return parent::_beforeDelete($object);
    }

    /**
     * Get maximum position of child categories by specific tree path
     *
     * @param string $path
     *
     * @return int
     */
    protected function getMaxPosition($path)
    {
        $connection    = $this->getConnection();
        $positionField = $connection->quoteIdentifier('position');
        $level         = count(explode('/', $path));
        $bind          = ['c_level' => $level, 'c_path' => $path . '/%'];
        $select        = $connection->select()->from(
            $this->getTable('sm_help_topic'),
            'MAX(' . $positionField . ')'
        )->where(
            $connection->quoteIdentifier('path') . ' LIKE :c_path'
        )->where($connection->quoteIdentifier('level') . ' = :c_level');

        $position = $connection->fetchOne($select, $bind);
        if (!$position) {
            $position = 0;
        }

        return $position;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var \SM\Help\Model\Topic $object */
        if (substr($object->getPath(), -1) == '/' || !$object->getPath()) {
            $object->setPath($object->getPath() . $object->getId());
            $this->savePath($object);
        }

        if ($object->dataHasChangedFor('parent_id')) {
            $newParent = ObjectManager::getInstance()
                ->create(\SM\Help\Model\Topic::class)
                ->load($object->getParentId());;
            $this->changeParent($object, $newParent);
        }

        if ($object->isObjectNew() || $object->getStoreId() == 0) {
            $stores = $this->storeManager->getStores();
            foreach ($stores as $store) {
                $this->saveDataByStore($object, $store->getId());
            }
            $this->saveDataByStore($object, 0);
        } else {
            $this->saveDataByStore($object, $object->getStoreId());
        }

        return parent::_afterSave($object);
    }

    /**
     * @param \SM\Help\Model\Topic $topic
     * @param int $storeId
     */
    protected function saveDataByStore(\SM\Help\Model\Topic $topic, $storeId)
    {
        $connection = $this->getConnection();
        $table      = $connection->getTableName('sm_help_topic_store');

        $data = [
            'store_id'    => $storeId,
            'topic_id'    => $topic->getId(),
            'name'        => $topic->getName(),
            'status'      => $topic->getStatus(),
            'description' => $topic->getDescription(),
        ];

        $connection->insertOnDuplicate($table, $data);
    }

    /**
     * Update path field
     * @param \SM\Help\Model\Topic $object
     * @return $this
     */
    protected function savePath($object)
    {
        if ($object->getId()) {
            $this->getConnection()->update(
                $this->getTable('sm_help_topic'),
                ['path' => $object->getPath()],
                ['topic_id = ?' => $object->getId()]
            );
            $object->unsetData('path_ids');
        }

        return $this;
    }

    /**
     * Move topic to another parent node
     *
     * @param \SM\Help\Model\Topic $topic
     * @param \SM\Help\Model\Topic $newParent
     * @param null|int $afterCategoryId
     *
     * @return $this
     */
    public function changeParent($topic, $newParent, $afterCategoryId = null)
    {
        $table         = $this->getTable('sm_help_topic');
        $connection    = $this->getConnection();
        $levelField    = $connection->quoteIdentifier('level');
        $pathField     = $connection->quoteIdentifier('path');

        $position = $this->processPositions($topic, $newParent, $afterCategoryId);

        $newPath          = sprintf('%s/%s', $newParent->getPath(), $topic->getId());
        $newLevel         = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $topic->getLevel();

        /**
         * Update children nodes path
         */
        $connection->update(
            $table,
            [
                'path'  => new Zend_Db_Expr(
                    'REPLACE(' . $pathField . ',' . $connection->quote(
                        $topic->getPath() . '/'
                    ) . ', ' . $connection->quote(
                        $newPath . '/'
                    ) . ')'
                ),
                'level' => new Zend_Db_Expr($levelField . ' + ' . $levelDisposition),
            ],
            [$pathField . ' LIKE ?' => $topic->getPath() . '/%']
        );

        /**
         * Update moved category data
         */
        $data = [
            'path'      => $newPath,
            'level'     => $newLevel,
            'position'  => $position,
            'parent_id' => $newParent->getId(),
        ];
        $connection->update($table, $data, ['topic_id = ?' => $topic->getId()]);

        $topic->addData($data);
        $topic->unsetData('path_ids');

        return $this;
    }

    /**
     * @param \SM\Help\Model\Topic $topic
     * @param \SM\Help\Model\Topic $newParent
     * @param null|int $afterCategoryId
     * @return int
     */
    protected function processPositions($topic, $newParent, $afterCategoryId)
    {
        $table         = $this->getTable('sm_help_topic');
        $connection    = $this->getConnection();
        $positionField = $connection->quoteIdentifier('position');

        $bind  = ['position' => new Zend_Db_Expr($positionField . ' - 1')];
        $where = [
            'parent_id = ?'         => $topic->getParentId(),
            $positionField . ' > ?' => $topic->getPosition(),
        ];
        $connection->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterCategoryId) {
            $select   = $connection->select()->from($table, 'position')->where('topic_id = :topic_id');
            $position = $connection->fetchOne($select, ['topic_id' => $afterCategoryId]);
            $position += 1;
        } else {
            $position = 1;
        }

        $bind  = ['position' => new Zend_Db_Expr($positionField . ' + 1')];
        $where = ['parent_id = ?' => $newParent->getId(), $positionField . ' >= ?' => $position];
        $connection->update($table, $bind, $where);

        return $position;
    }
}

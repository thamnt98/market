<?php
namespace SM\FlashSale\Block\Adminhtml\Event\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Tree;
use Magento\CatalogEvent\Helper\Adminhtml\Event;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;

class Category extends \Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Category{

    protected $_categoryFactory;
    public function __construct(Context $context, Tree $categoryTree, Registry $registry, CategoryFactory $categoryFactory, EncoderInterface $jsonEncoder, Event $eventAdminhtml, array $data = [])
    {
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $categoryTree, $registry, $categoryFactory, $jsonEncoder, $eventAdminhtml, $data);
    }

    /**
     * Convert categories tree to array recursively
     *
     * @param Node $node
     * @return array
     */
    protected function _getNodesArray($node)
    {
        $category = $this->_categoryFactory->create()->load($node->getId());
        $eventHelper = $this->_eventAdminhtml;
        $result = [
            'id' => (int)$node->getId(),
            'parent_id' => (int)$node->getParentId(),
            'children_count' => (int)$node->getChildrenCount(),
            'is_active' => (bool)$node->getIsActive(),
            'disabled' => $node->getLevel() <= 1 || in_array($node->getId(), $eventHelper->getInEventCategoryIds()),
            'name' => $node->getName(),
            'level' => (int)$node->getLevel(),
            'product_count' => (int)$node->getProductCount(),
            'is_flashsale' => (int) $category->getData('is_flashsale')
        ];
        if ($node->hasChildren()) {
            $result['children'] = [];
            foreach ($node->getChildren() as $childNode) {
                $result['children'][] = $this->_getNodesArray($childNode);
            }
        }
        $result['cls'] = ($result['is_active'] ? '' : 'no-') . 'active-category';
        if ($result['disabled']) {
            $result['cls'] .= ' em';
        }
        $result['expanded'] = false;
        if (!empty($result['children'])) {
            $result['expanded'] = true;
        }
        return $result;
    }

    public function getCheck(){
        return "Fuck This Shit";
    }

}
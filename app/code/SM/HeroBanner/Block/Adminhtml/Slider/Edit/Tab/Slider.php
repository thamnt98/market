<?php


namespace SM\HeroBanner\Block\Adminhtml\Slider\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mageplaza\BannerSlider\Model\Config\Source\Location;

class Slider extends \Mageplaza\BannerSlider\Block\Adminhtml\Slider\Edit\Tab\Slider
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Enabledisable $statusOptions,
        Location $location,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter,
        Store $systemStore,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $statusOptions, $location, $groupRepository,
            $searchCriteriaBuilder, $objectConverter, $systemStore, $data);
        $this->categoryFactory = $categoryFactory;
    }

    public function _prepareForm()
    {
        $return = parent::_prepareForm();
        $fieldset = $this->getForm()->getElement('base_fieldset');
        $fieldset->addField(
            'category',
            'select',
            [
                'label' => __('Categories'),
                'title' => __('Categories'),
                'name' => 'category',
                'values' => $this->getCategories(),
            ]
        );
        /** @var \Mageplaza\BannerSlider\Model\Slider $slider */
        $slider = $this->_coreRegistry->registry('mpbannerslider_slider');
        $this->getForm()->setValues($slider->getData());
        return $return;

    }

    public function getCategories()
    {

        $categoriesArray = $this->categoryFactory->create()
                                                            ->addAttributeToSelect('name')
                                                            ->addAttributeToSort('path', 'asc')
                                                            ->load()
                                                            ->toArray();

        $categories[]=[
            'label' => 'Home page',
            'value' => 'home-page'
        ];
        foreach ($categoriesArray as $categoryId => $category) {
            if (isset($category['name']) && isset($category['level'])) {
                $categories[] = array(
                    'label' => $category['name'],
                    'level' => $category['level'],
                    'value' => $categoryId,
                );
            }
        }

        return $categories;
    }

}

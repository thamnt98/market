<?php

declare(strict_types=1);

namespace SM\Search\Block\Form;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SM\Category\Model\Repository\CategoryRepository;

/**
 * Class ForgotPassword
 *
 * @package SM\Customer\Block\Form
 */
class MiniForm extends Template
{
    const CATEGORY_FIELD_NAME = 'cat';

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * MiniForm constructor.
     * @param Context $context
     * @param CategoryRepository $categoryRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        CategoryRepository $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getCategories(): array
    {
        return $this->categoryRepository->getCategoriesInSearchForm();
    }

    /**
     * @param array $categoryData
     * @return bool
     */
    public function isSelectedCategory(array $categoryData): bool
    {
        return $categoryData['entity_id'] == $this->getRequest()->getParam(self::CATEGORY_FIELD_NAME);
    }
}

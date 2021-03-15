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
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * MiniForm constructor.
     * @param Context $context
     * @param CategoryRepository $categoryRepository
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        Context $context,
        CategoryRepository $categoryRepository,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->categoryRepository = $categoryRepository;
        $this->httpContext = $httpContext;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getCategories(): array
    {
        return $this->categoryRepository->getCategoriesInSearchForm();
    }

    public function getSelectedCategory()
    {
        if ($this->getRequest()->getFullActionName() === 'catalogsearch_result_index' &&
            $this->getRequest()->getParam('cat')
        ) {
            foreach ($this->getCategories() as $category) {
                if (isset($category['entity_id'])
                    && $category['entity_id'] == $this->getRequest()->getParam('cat')
                ) {
                    return $category;
                }
            }
        }
    }

    /**
     * Checking customer register status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
}

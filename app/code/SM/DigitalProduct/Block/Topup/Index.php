<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Block\Topup;

use SM\DigitalProduct\Model\CategoryRepository;

/**
 * Class Index
 * @package SM\DigitalProduct\Block\Topup
 */
class Index extends \SM\DigitalProduct\Block\Index
{
    /**
     * @return \SM\DigitalProduct\Block\Index|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        $categoryContent = $this->categoryRepository->getCategoryContent(
            CategoryRepository::TOPUP
        );
        $this->setCategoryContent($categoryContent);
    }
}

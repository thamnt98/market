<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Block\MobilePackage;

use SM\DigitalProduct\Model\CategoryRepository;

/**
 * Class Index
 * @package SM\DigitalProduct\Block\MobilePackage
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
            CategoryRepository::MOBILE_PACKAGE
        );
        $this->setCategoryContent($categoryContent);
    }
}

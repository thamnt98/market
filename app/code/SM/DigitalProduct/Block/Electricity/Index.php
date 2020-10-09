<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Block\Electricity;

use SM\DigitalProduct\Api\Data\CategoryContentInterface;
use SM\DigitalProduct\Helper\Category\Data;
use SM\DigitalProduct\Model\CategoryRepository;

/**
 * Class Index
 * @package SM\DigitalProduct\Block\Electricity
 */
class Index extends \SM\DigitalProduct\Block\Index
{
    /**
     * @var CategoryContentInterface
     */
    private $contentBill;

    /**
     * @var CategoryContentInterface
     */
    private $contentToken;

    protected function _prepareLayout()
    {
        $contentBill = $this->categoryRepository->getCategoryContent(
            Data::ELECTRICITY_BILL_VALUE
        );

        $contentToken = $this->categoryRepository->getCategoryContent(
            Data::ELECTRICITY_TOKEN_VALUE
        );
        $this->setContentBill($contentBill);
        $this->setContentToken($contentToken);
    }
    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return strtok($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]), "?");
    }

    /**
     * @return \SM\DigitalProduct\Api\Data\C1CategoryDataInterface[]
     */
    public function getC1Categories()
    {
        return [
            $this->digitalProductRepository->getProductsByCategory(Data::ELECTRICITY_TOKEN_VALUE),
            $this->digitalProductRepository->getProductsByCategory(Data::ELECTRICITY_BILL_VALUE)
        ];
    }

    /**
     * @return CategoryContentInterface
     */
    public function getContentBill()
    {
        return $this->contentBill;
    }

    /**
     * @param CategoryContentInterface $contentBill
     * @return $this
     */
    public function setContentBill($contentBill)
    {
        $this->contentBill = $contentBill;
        return $this;
    }

    /**
     * @return CategoryContentInterface
     */
    public function getContentToken()
    {
        return $this->contentToken;
    }

    /**
     * @param CategoryContentInterface $contentToken
     * @return $this
     */
    public function setContentToken($contentToken)
    {
        $this->contentToken = $contentToken;
        return $this;
    }
}

<?php

namespace SM\Review\Ui\DataProvider\EditReview\Form;

use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use SM\Review\Api\Data\ReviewImageInterface;
use SM\Review\Model\ResourceModel\ReviewEdit\CollectionFactory as ReviewEditCollectionFactory;
use SM\Review\Model\ResourceModel\ReviewImage\Collection as ReviewImageCollection;
use SM\Review\Model\ResourceModel\ReviewImage\CollectionFactory as ReviewImageCollectionFactory;
use SM\Review\Model\ReviewEdit;

/**
 * Class DataProvider
 * @package SM\Review\Ui\DataProvider\EditReview\Form
 */
class DataProvider extends AbstractDataProvider
{
    protected $loadedData;

    /**
     * @var UrlInterface
     */
    protected $urlInterface;
    /**
     * @var PoolInterface
     */
    protected $pool;

    protected $reviewImageCollectionFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReviewEditCollectionFactory $reviewEditCollectionFactory
     * @param ReviewImageCollectionFactory $reviewImageCollectionFactory
     * @param PoolInterface $pool
     * @param UrlInterface $urlInterface
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReviewEditCollectionFactory $reviewEditCollectionFactory,
        ReviewImageCollectionFactory $reviewImageCollectionFactory,
        PoolInterface $pool,
        UrlInterface $urlInterface,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $reviewEditCollectionFactory->create();
        $this->pool = $pool;
        $this->urlInterface = $urlInterface;
        $this->reviewImageCollectionFactory = $reviewImageCollectionFactory;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();

        /** @var ReviewImageCollection $reviewImageCollection */
        $reviewImageCollection = $this->reviewImageCollectionFactory->create();

        /** @var ReviewEdit $reviewEdit */
        foreach ($items as $reviewEdit) {
            $this->loadedData[$reviewEdit->getId()] = $reviewEdit->getData();
            $this->loadedData[$reviewEdit->getId()]["review_url"]
                = $this->urlInterface->getUrl("review/product/edit", ["id" => $reviewEdit->getReviewId()]);
            $this->loadedData[$reviewEdit->getId()]["vote_value"] *= 20;

            $reviewImageCollection->addFieldToFilter("review_id", ["eq" => $reviewEdit->getReviewId()]);
            $reviewImageCollection->addFieldToFilter("is_edit", ["eq" => 1]);

            $this->loadedData[$reviewEdit->getId()]["images"] = [];
            /** @var ReviewImageInterface $image */
            foreach ($reviewImageCollection as $image) {
                $imageDataAsArray = json_decode($image->getImage(), true);
                $imageUrl = $this->urlInterface->getBaseUrl() . "media/sm/review/images" . $imageDataAsArray["file"];
                $this->loadedData[$reviewEdit->getId()]["images"][] = $imageUrl;
            }
        }
        return $this->loadedData;
    }
}

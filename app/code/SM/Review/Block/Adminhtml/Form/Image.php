<?php

namespace SM\Review\Block\Adminhtml\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use SM\Review\Api\Data\ReviewImageInterface;
use SM\Review\Api\ReviewEditRepositoryInterface;
use SM\Review\Api\ReviewImageRepositoryInterface;
use SM\Review\Model\ReviewImageRepository;

/**
 * Class Image
 * @package SM\Review\Block\Adminhtml\Form
 */
class Image extends \Magento\Backend\Block\Template
{
    const IMAGE_MAX_WIDTH = 200;
    const IMAGE_MAX_HEIGHT = 200;

    /**
     * @var array
     */
    protected $images;
    /**
     * @var ReviewImageRepositoryInterface
     */
    protected $reviewImageRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var ReviewEditRepositoryInterface
     */
    protected $reviewEditRepository;

    /**
     * Image constructor.
     * @param Context $context
     * @param ReviewImageRepositoryInterface $reviewImageRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ReviewEditRepositoryInterface $reviewEditRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ReviewImageRepositoryInterface $reviewImageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ReviewEditRepositoryInterface $reviewEditRepository,
        array $data = []
    ) {
        $this->reviewEditRepository = $reviewEditRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->reviewImageRepository = $reviewImageRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Backend\Block\Template|void
     */
    protected function _prepareLayout()
    {
        if ($this->getRequest()->getControllerName() == "customer") {
            $entityId = $this->getRequest()->getParam("id");
            $review = $this->reviewEditRepository->getById($entityId);
            $images = $review->getImages();
        } else {
            $reviewId = $this->getRequest()->getParam("id");
            $this->searchCriteriaBuilder->addFilter("is_edit", ReviewImageRepository::NOT_EDIT);
            $this->searchCriteriaBuilder->addFilter("review_id", $reviewId);
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $images = $this->reviewImageRepository->getList($searchCriteria)->getItems();
        }
        $this->setImages($images);
    }

    /**
     * @param array $images
     * @return $this
     */
    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $html = "";
        if (count($this->getImages())) {
            /** @var ReviewImageInterface $image */
            foreach ($this->getImages() as $image) {
                $imageParts = json_decode($image->getImage(), true);
                if (isset($imageParts["url"])) {
                    $url = $imageParts["url"];
                    $html .= '<a href="' . $url . '"><img style="max-height:' . self::IMAGE_MAX_HEIGHT . 'px;max-width:' . self::IMAGE_MAX_WIDTH . 'px;" src="' . $url . '" alt="' . __("review image") . '" /></a>';
                }
            }
        } else {
            $html .= "<em>" . __("No Images.") . "</em>";
        }

        return $html;
    }
}

<?php
/**
 * @category Magento
 * @package SM\Review\Controller\Form
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Review\Controller\Form;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use SM\Review\Api\Data\ReviewImageInterface;
use SM\Review\Model\ReviewedRepository;
use SM\Review\Model\ReviewImageRepository;

/**
 * Class GetImage
 * @package SM\Review\Controller\Form
 */
class GetImage extends Action
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var ReviewedRepository
     */
    protected $reviewedRepository;
    /**
     * @var ReviewImageRepository
     */
    protected $reviewImageRepository;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * GetImage constructor.
     * @param Context $context
     * @param ReviewImageRepository $reviewImageRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ReviewedRepository $reviewedRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        ReviewImageRepository $reviewImageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ReviewedRepository $reviewedRepository,
        JsonFactory $jsonFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->reviewedRepository = $reviewedRepository;
        $this->reviewImageRepository = $reviewImageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();
        $postData = $this->getRequest()->getParams();
        if (isset($postData["review_id"])) {
            try {
                $reviewId = $postData["review_id"];
                $reviewDetail = $this->reviewedRepository->getById($reviewId);
                $images = [];
                /** @var ReviewImageInterface $image */
                foreach ($reviewDetail->getImages() as $image) {
                    $images[] = $image->getImage();
                }
                $result->setData($images);
            } catch (Exception $e) {
                $result->setData([]);
                return $result;
            }
        } else {
            $result->setData([]);
        }
        return $result;
    }
}

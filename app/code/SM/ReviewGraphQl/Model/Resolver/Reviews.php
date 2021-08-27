<?php

declare(strict_types=1);

namespace SM\ReviewGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use SM\Review\Model\ReviewRepository;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class Reviews implements ResolverInterface

{
    /**
     * @var ReviewRepository
     */
    protected $reviewRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    public function __construct(
        ReviewRepository $reviewRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->reviewRepository = $reviewRepository;
    }


    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->vaildateArgs($args);
        $searchCriteria = $this->searchCriteriaBuilder->build('product_review', $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);
        $filterData = $this->hasFilterRating($searchCriteria);

        $searchResults = $this->reviewRepository->getList($filterData['searchCriteria']);
        $items = $this->setItemImage($searchResults->getItems());

        if ($filterData['rating']) {
            $items = $this->filterRating($filterData['rating'], $items);
        }

        return [
            'total_count' => count($items),
            'items' => $items,
        ];
    }

    /**
     * @param $items
     * @return array
     */
    protected function setItemImage($items): array
    {
        $data = [];
        $dataImage = [];
        foreach ($items as $item) {
            $images = $item->getImages();
            if ($images) {
                foreach ($images as $image) {
                    $dataImage[]['url'] =  $image;
                }
            }
            $item->setImages($dataImage);
            $data[] = $item;
        }
        return $data;
    }

    /**
     * @param $rating
     * @param $items
     * @return array
     */
    protected  function filterRating($rating, $items)
    {
        $data = [];
        foreach ($items as $item) {
           if ($rating == $item->getRating()) {
               $data[] = $item;
           }
        }
        return $data;
    }

    /**
     * @param $searchCriteria
     * @return array
     */
    protected  function hasFilterRating($searchCriteria)
    {
        $rating = null;
        $data = [];
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == 'rating') {
                    $rating = $filter->getValue();
                } else {
                    $data[] = $filter;
                }
            }
            $filterGroup->setFilters($data);
        }

        return [
            'rating' => $rating,
            'searchCriteria' => $searchCriteria
        ];
    }

    /**
    * @param array $args
    * @throws GraphQlInputException
    */
    private function vaildateArgs(array $args): void
    {
        if (isset($args['currentPage']) && $args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }

        if (isset($args['pageSize']) && $args['pageSize'] < 1) {
                throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
    }
}

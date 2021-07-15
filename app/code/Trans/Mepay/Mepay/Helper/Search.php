<?php 
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;

class Search extends AbstractHelper
{
  /**
   * @var \SearchCriteriaBuilder
   */
  protected $searchBuilder;

  /**
   * @var \SortOrderBuilder
   */
  protected $sortBuilder;

  /**
   * Constructor
   * @param Context $context
   * @param SearchCriteriaBuilder $searchBuilder
   * @param SortOrderBuilder $sortBuilder
   */
  public function __construct(
    Context $context,
    SearchCriteriaBuilder $searchBuilder,
    SortOrderBuilder $sortBuilder
  ) {
    $this->searchBuilder = $searchBuilder;
    $this->sortBuilder = $sortBuilder;
    parent::__construct($context);
  }

  /**
   * Get search criteria
   * @param  array $filters
   * @return \Magento\Framework\Api\SearchCriteriaInterface
   */
  public function getSearchCriteria($filters = [])
  {
    if (is_array($filters)) {
      foreach ($filters as $key => $value) {
        $this->searchBuilder->addFilter($key, $value);
      }
    }

    return $this->searchBuilder->create();
  }

  /**
   * Get single sort
   * @param  string $field
   * @param  string $direction
   * @return \Magento\Framework\Api\SortOrder
   */
  public function getSortSingle($field, $direction)
  {
    return $this->sortBuilder->setField($field)->setDirection($direction)->create();
  }

  /**
   * Get multiple sort
   * @param  array  $orders
   * @return \Magento\Framework\Api\SortOrder[]
   */
  public function getSortMultiple($orders = [])
  {
    $results = [];
    if(is_array($orders)) {
      foreach ($orders as $key => $value) {
        $results[] = $this->getSortSingle($key, $value);
      }
    }

    return $results;
  }

  /**
   * Get search criteria sorted by
   * @param  array $filters
   * @param  string $field
   * @param  string $direction
   * @return \Magento\Framework\Api\SearchCriteriaInterface
   */
  public function getSearchCriteriaSortedBy($filters, $field, $direction)
  {
    return $this->getSearchCriteria($filters)->setSortOrders($this->getSortMultiple([$field => $direction]));
  }
}
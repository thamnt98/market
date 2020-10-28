<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author  Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Model\ResourceModel;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Trans\Mepay\Api\Data\ConfigPullStatusInterface;
use Trans\Mepay\Api\Data\ConfigPullStatusInterfaceFactory as ModelFactory;
use Trans\Mepay\Model\ResourceModel\ConfigPullStatus as ResourceModel;
use Trans\Mepay\Api\ConfigPullStatusRepositoryInterface;

class ConfigPullStatusRepository implements ConfigPullStatusRepositoryInterface
{
  const NOT_FOUND_MSG = 'Requested Object doesn\'t exist';
  protected $resource;

  protected $modelFactory;

  public function __construct(
    ResourceModel $resource,
    ModelFactory $modelFactory
  ) {
    $this->resource = $resource;
    $this->modelFactory = $modelFactory;
  }
  /**
   * @inheritdoc
   */
  public function get(string $name)
  {
    $model = $this->modelFactory->create();
    $this->resource->load($model, $name, ConfigPullStatusInterface::CONFIG_NAME);
    if ($model->getId()) {
      return $model;
    }
    throw new NoSuchEntityException(__(self::NOT_FOUND_MSG));
  }

  /**
   * @inheritdoc
   */
  public function getById(int $id)
  {
    $model = $this->modelFactory->create();
    $this->resource->load($model, $id);
    if ($model->getId()) {
      return $model;
    }
    throw new NoSuchEntityException(__(self::NOT_FOUND_MSG));

  }

  /**
   * @inheritdoc
   */
  public function save(ConfigPullStatusInterface $config)
  {
    try {
      $this->resource->save($config);
    } catch (\Exception $e) {
      throw new CouldNotSaveException(__($e->getMessage()));
    }
  }

  /**
   * @inheritdoc
   */
  public function delete(ConfigPullStatusInterface $config)
  {
    try {
      $this->resource->delete($config);
    } catch (\Exception $e) {
      throw new StateException(__($e->getMessage()));
    }
  }
}
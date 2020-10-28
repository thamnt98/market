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
namespace Trans\Mepay\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Trans\Mepay\Api\ConfigPullStatusRepositoryInterface as Repo;
use Trans\Mepay\Api\Data\ConfigPullStatusInterfaceFactory as ModelFactory;

class ConfigNamePullStatusTransaction implements DataPatchInterface 
{
  /**
   * @var  string
   */
  const CONFIG_PULL_STATUS = 'config_pull_status_cronjob';

  /**
   * @var  string
   */
  protected $moduleDataSetup;

  /**
   * @var  string
   */
  protected $repo;

  /**
   * @var  string
   */
  protected $modelFactory;

  /**
   * Constructor
   * @param ModuleDataSetupInterface $moduleDataSetup
   * @param Repo                     $repo
   * @param ModelFactory             $modelFactory
   */
  public function __construct(
    ModuleDataSetupInterface $moduleDataSetup,
    Repo $repo,
    ModelFactory $modelFactory
  ) {
    $this->moduleDataSetup = $moduleDataSetup;
    $this->repo = $repo;
    $this->modelFactory = $modelFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function apply()
  {
    $this->moduleDataSetup->startSetup();
    $model = $this->modelFactory->create();
    $model->setConfigName(self::CONFIG_PULL_STATUS);
    $this->repo->save($model);
    $this->moduleDataSetup->endSetup();
  }

  /**
   * {@inheritdoc}
   */
   public function getAliases()
   {
      return [];
  }
 
   /**
    * {@inheritdoc}
    */
   public static function getDependencies()
   {
     return [];
   } 
}
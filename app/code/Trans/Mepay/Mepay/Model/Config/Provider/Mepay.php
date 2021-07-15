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
namespace Trans\Mepay\Model\Config\Provider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class Mepay implements ConfigProviderInterface
{
  const CODE = 'trans_mepay';

  /**
   * @var ScopeConfigInterface
   */
  protected $scopeConfig;

  /**
   * @var StoreManagerInterface
   */
  private $storeManager;

  /**
   * @param ScopeConfigInterface $scopeConfig
   * @param StoreManagerInterface $storeManager
   */
  public function __construct(
      ScopeConfigInterface $scopeConfig,
      StoreManagerInterface $storeManager
  ) {
      $this->scopeConfig = $scopeConfig;
      $this->storeManager = $storeManager;
  }

  /**
   * @return array
   */
  public function getConfig()
  {
      $providers = $this->getProviders($this->scopeConfig->getValue(
          'payment',
          \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
          $this->storeManager->getStore()->getId()
      ));
      $this->unifyProviderConfig($providers);

      return [
          'payment' => [
              self::CODE => [
                  'providers' => $providers,
              ]
          ]
      ];
  }

  /**
   * @param array $scopeConfig
   * @return array
   */
  protected function getProviders(array $scopeConfig)
  {
      $prefix = self::CODE . '_';

      $params = array_filter(
          $scopeConfig,
          function (array $data, $key) use ($prefix) {
              return (strpos($key, $prefix) === 0 && (int)$data['active']);
          },
          ARRAY_FILTER_USE_BOTH
      );

      $providers = array_map(
          function ($key, array $data) {
              $data['name'] = $key;
              return $data;
          }, array_keys($params), $params);

      return $providers;
  }

  /**
   * @param array $providers
   */
  protected function unifyProviderConfig(array &$providers)
  {
      $keys = $this->collectProviderConfigKeys($providers);

      array_walk($providers, function (array &$provider) use ($keys) {
          $provider = array_merge($keys, $provider);
      });
  }

  /**
   * @param array $providers
   * @return array
   */
  protected function collectProviderConfigKeys(array $providers)
  {
      $keys = [];

      array_walk($providers, function (array $provider) use (&$keys) {
          foreach (array_keys($provider) as $key) {
              if (array_key_exists($key, $keys)) {
                  continue;
              }
              $keys[$key] = null;
          }
      });
      return $keys;
  }

  /**
   * @param $code
   * @return array
   */
  public function getProviderConfig($code)
  {
      $config = $this->getConfig();

      foreach ($config['payment'][self::CODE]['providers'] as $providerConfig) {
          if ($providerConfig['name'] === $code) {
              return array_merge($this->getCommonConfig(), $providerConfig);
          }
      }
      return [];
  }

  /**
   * @return array
   */
  protected function getCommonConfig()
  {
      $scopeConfig = $this->scopeConfig->getValue(
          'payment',
          \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
          $this->storeManager->getStore()->getId()
      );
      return (array)$scopeConfig[self::CODE];
  }

}

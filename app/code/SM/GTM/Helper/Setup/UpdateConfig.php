<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_GTM
 *
 * Date: April, 04 2020
 * Time: 3:17 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\GTM\Helper\Setup;

class UpdateConfig
{
    const CONFIG_DATA_TBL = 'core_config_data';

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * UpdateConfig constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->connection = $resource->getConnection(
            \Magento\Framework\Setup\ModuleDataSetupInterface::DEFAULT_SETUP_CONNECTION
        );
    }

    /**
     * Add new gtm events.
     *
     * @param string $handler
     * @param array  $trigger
     * @param string $key
     * @param array  $templateData
     *
     * @throws \Zend_Json_Exception
     */
    public function add($handler, $trigger, $key, $templateData)
    {
        $select = $this->connection->select()
            ->from(self::CONFIG_DATA_TBL)
            ->where('path = \'' . \SM\GTM\Helper\VariableMapping::XPATH_MAPPING_VARIABLES . '\'');
        $data = $this->connection->fetchAssoc($select);
        foreach ($data as $item) {
            try {
                $value = \Zend_Json_Decoder::decode($item['value']);
                $value[] = [
                    'frontend_handler' => $handler,
                    'event_trigger'    => \Zend_Json_Encoder::encode($trigger),
                    'gtm_key'          => $key,
                    'template'         => \Zend_Json_Encoder::encode($templateData)
                ];
                $item['value'] = \Zend_Json_Encoder::encode($value);

                $this->connection->insertOnDuplicate(self::CONFIG_DATA_TBL, $item, ['value']);
            } catch (\Zend_Json_Exception $e) {
                throw $e;
            }
        }
    }

    /**
     * @param array $data | [ ['handler' => '', 'trigger' => [], 'key' => '', 'templateData' => []] ]
     *
     * @throws \Zend_Json_Exception
     * @throws \Exception
     */
    public function addEvents($data)
    {
        if (!is_array($data)) {
            throw new \Exception('Add GTM Event config error data.');
        }

        foreach ($data as $item) {
            if (empty($item['trigger']) ||
                !is_array($item['trigger']) ||
                empty($item['key']) ||
                empty($item['templateData']) ||
                !is_array($item['templateData'])
            ) {
                throw new \Exception('Add GTM Event config error data.');
            }

            $this->add($item['handler'] ?? 'default', $item['trigger'], $item['key'], $item['templateData']);
        }
    }
}

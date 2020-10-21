<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: October, 15 2020
 * Time: 5:35 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Ui\Component\Form\Modify\Columns;

class RedirectKey implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    /**
     * @var array
     */
    protected $options;

    public function __construct(
        $options = []
    ) {
        $this->options = $options;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        $meta['web'] = [
            'children' => [
                'redirect_id' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'allOptions'    => json_encode($this->getOptions()),
                            ],
                        ],
                    ]
                ],
            ]
        ];

        return $meta;
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        $data = [];

        foreach ($this->options as $key => $option) {
            if ($option instanceof \Magento\Framework\Data\OptionSourceInterface) {
                $data[$key] = $option->toOptionArray();
            }
        }

        return $data;
    }
}

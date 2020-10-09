<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: October, 07 2020
 * Time: 3:04 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Source;

use Magento\Framework\Exception\LocalizedException;

class Customers implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var array|null
     */
    protected $options = null;

    /**
     * Segments constructor.
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $options = [];
            /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $coll */
            $coll = $this->collectionFactory->create();
            $coll->getSelect()->where('is_active = ?', 1);
            try {
                $coll->addAttributeToSelect('telephone');
            } catch (\Exception $e) {
            }

            /** @var \Magento\Customer\Model\Customer $customer */
            foreach ($coll->getItems() as $customer) {
                try {
                    $name = $customer->getName() . " ({$customer->getEmail()})";
                } catch (LocalizedException $e) {
                    $name = $customer->getEmail();
                }

                if ($customer->getData('telephone')) {
                    $name .= " - {$customer->getData('telephone')}";
                }

                $options[] = [
                    'label' => $name,
                    'value' => $customer->getId(),
                ];
            }

            $this->options = $options;
        }

        return $this->options;
    }
}

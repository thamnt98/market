<?php


namespace SM\Checkout\Model;


use Magento\Sales\Model\OrderFactory;
use Magento\Setup\Exception;
use SM\Checkout\Api\OrderInterface;

class Order implements OrderInterface
{
    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    public function __construct(OrderFactory $orderFactory)
    {
        $this->orderFactory = $orderFactory;
    }

    public function getStatus($customerId, $orderId)
    {
        $dataResponse = [
            'error' => true,
            'data'  => []
        ];
        try {
            $order = $this->orderFactory->create()->load($orderId);
        } catch (\Exception $exception) {
            $dataResponse['status'] = 'not_found';
            return json_encode($dataResponse);
        }

        if ($order->getCustomerId() == $customerId) {
            $dataResponse = [
                'error'  => false,
                'status' => 'success',
                'data'   => [
                    'status'   => $order->getStatus(),
                    'state'    => $order->getState(),
                    'order_id' => $orderId
                ]
            ];
        }
        return json_encode($dataResponse);
    }

}

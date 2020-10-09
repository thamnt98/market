<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Controller;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;

/**
 * Class Router
 * @package SM\Help\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \SM\Help\Model\Url
     */
    protected $url;

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Router constructor.
     * @param \SM\Help\Model\Url $url
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \SM\Help\Model\Url $url,
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->url = $url;
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritDoc
     */
    public function match(RequestInterface $request)
    {
        /** @var Http $request */

        $identifier = trim($request->getPathInfo(), '/');
        $this->eventManager->dispatch('core_controller_router_match_before', [
            'router'    => $this,
            'condition' => new DataObject(['identifier' => $identifier, 'continue' => true]),
        ]);

        $pathInfo = $request->getPathInfo();

        $result = $this->url->match($pathInfo);

        if ($result) {
            $params = $result->getParams();

            $request->setModuleName($result->getModuleName())
                ->setControllerName($result->getControllerName())
                ->setActionName($result->getActionName())
                ->setParams($params);

            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
            );
        }

        return false;
    }
}

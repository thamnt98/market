<?php

/**
 * @category SM
 * @package SM_TodayDeal
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\TodayDeal\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var \SM\TodayDeal\Model\PageFactory
     */
    protected $_postFactory;

    /**
     * Config primary
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Url
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \SM\TodayDeal\Model\PostFactory $postFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \SM\TodayDeal\Model\PostFactory $postFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_url          = $url;
        $this->_postFactory  = $postFactory;
        $this->_storeManager = $storeManager;
        $this->_response     = $response;
    }

    /**
     * Validate and Match Today Deals Post and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {

        $identifier = trim($request->getPathInfo(), '/');
        $identifier = explode('/', $identifier);
        if (isset($identifier[0])) {
            unset($identifier[0]);
        }
        $identifier = implode('/', $identifier);

        /** @var \Sm\TodayDeal\Model\Post $post */
        $post = $this->_postFactory->create();
        $post = $post->checkIdentifier($identifier, $this->_storeManager->getStore()->getId());
        if (!isset($post["post_id"])) {
            return null;
        }

        if (isset($post["is_redirect_to_plp"]) && $post["is_redirect_to_plp"]) {
            $request->setModuleName('catalog')
                ->setControllerName('category')
                ->setActionName('view')
                ->setParam('id', $post["category_to_redirect"]);
        } else {
            $request->setModuleName('todaydeal')
                ->setControllerName('post')
                ->setActionName('view')
                ->setParam('post_id', $post["post_id"]);
        }

        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

        return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
    }
}

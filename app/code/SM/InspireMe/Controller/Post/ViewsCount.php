<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Controller\Post;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;

/**
 * Class ViewsCount
 * @package SM\InspireMe\Controller\Post
 */
class ViewsCount extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Mirasvit\Blog\Api\Repository\PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * ViewsCount constructor.
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mirasvit\Blog\Api\Repository\PostRepositoryInterface $postRepository
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mirasvit\Blog\Api\Repository\PostRepositoryInterface $postRepository
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->postRepository = $postRepository;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        /** @var \Mirasvit\Blog\Model\Post $post */
        $post = $this->_initPost();
        if ($post && $post->getId()) {
            $tempViewsCount = $post->getTempViewsCount();
            $post->setTempViewsCount($tempViewsCount + 1);
            $post->setFlagViewsChanged(1);
            $this->postRepository->save($post);
        }
    }

    /**
     * @return \Mirasvit\Blog\Model\Post
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _initPost()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();

        /** @var \Mirasvit\Blog\Model\Post $post */
        $post = $this->postRepository->get($id);

        return $post;
    }
}

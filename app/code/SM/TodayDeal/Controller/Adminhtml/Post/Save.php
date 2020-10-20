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

namespace SM\TodayDeal\Controller\Adminhtml\Post;

use Magento\Cms\Controller\Adminhtml\Page\PostDataProcessor;
use SM\TodayDeal\Api\PostRepositoryInterface;
use SM\TodayDeal\Model\Post;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\TodayDeal\Model\PostFactory;

/**
 * Save Today Deals post action.
 */

class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SM_TodayDeal::save';

    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    protected $date;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     * @param PostFactory|null $postFactory
     * @param PostRepositoryInterface|null $postRepository
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        DataPersistorInterface $dataPersistor,
        PostFactory $postFactory = null,
        PostRepositoryInterface $postRepository = null
    ) {
        $this->date = $date;
        $this->dataProcessor  = $dataProcessor;
        $this->dataPersistor  = $dataPersistor;
        $this->postFactory    = $postFactory ?: ObjectManager::getInstance()->get(PostFactory::class);
        $this->postRepository = $postRepository
            ?: ObjectManager::getInstance()->get(PostRepositoryInterface::class);
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $date = $this->date->date();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (!$this->validatePeriodDate($data)) {
                $this->messageManager->addErrorMessage(__('Publish To must be greater than Publish From'));
                return $resultRedirect->setPath('*/*/edit', ['post_id' => $this->getRequest()->getParam('post_id')]);
            }

            $data = $this->dataProcessor->filter($data);
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Post::STATUS_ENABLED;
            }
            if (empty($data['post_id'])) {
                $data['post_id'] = null;
            }

            $this->filterDataImage($data, 'mb_image', 'mb_image_path');
            $this->filterDataVideo($data);
            $this->filterDataProduct($data);

            //Save post thumbnail
            if (isset($data['thumbnail'][0])) {
                if (isset($data['thumbnail'][0]['name'])) {
                    $data['thumbnail_name'] = $data['thumbnail'][0]['name'];
                }

                if (isset($data['thumbnail'][0]['path'])) {
                    $data['thumbnail_path'] = $data['thumbnail'][0]['path'];
                } elseif (isset($data['thumbnail'][0]['url'])) {
                    $path = explode('/', $data['thumbnail'][0]['url']);
                    unset($path[0]);
                    unset($path[1]);
                    $data['thumbnail_path'] = implode('/', $path);
                }

                $data['thumbnail_size'] = $data['thumbnail'][0]['size'];
                unset($data['thumbnail']);
            }

            if (isset($data['update_time'])) {
                $data['update_time'] = $date;
            }

            if (empty($data['publish_from'])) {
                $data['publish_from'] = $date;
            }

            if (empty($data['publish_to'])) {
                $data['publish_to'] = $date;
            }

            /** @var \SM\TodayDeal\Model\Post $model */
            $model = $this->postFactory->create();

            $id = $this->getRequest()->getParam('post_id');
            if ($id) {
                try {
                    $model = $this->postRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $data['layout_update_xml'] = $model->getLayoutUpdateXml();
            $data['custom_layout_update_xml'] = $model->getCustomLayoutUpdateXml();
            $model->setData($data);

            if ($this->isUrlKeyExist($model, $model->getIdentifier(), $id)) {
                $this->messageManager->addErrorMessage(__('The post URL Key already exists.'));
                return $resultRedirect->setPath('*/*/edit', ['post_id' => $this->getRequest()->getParam('post_id')]);
            }

            try {
                $this->postRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the post.'));
                return $this->processResultRedirect($model, $resultRedirect, $data, $date);
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the post.'));
            }

            $this->dataPersistor->set('today_deals_post', $data);
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $this->getRequest()->getParam('post_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process result redirect
     *
     * @param \SM\TodayDeal\Api\Data\PostInterface $model
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirect
     * @param array $data
     * @param $date
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws LocalizedException
     */
    private function processResultRedirect($model, $resultRedirect, $data, $date)
    {
        if ($this->getRequest()->getParam('back', false) === 'duplicate') {
            $newPost = $this->postFactory->create(['data' => $data]);
            $newPost->setId(null);
            $identifier = $model->getIdentifier() . '-' . uniqid();
            $newPost->setIdentifier($identifier);
            $newPost->setIsActive(false);
            $newPost->setCreationTime($date);
            $this->postRepository->save($newPost);
            $this->messageManager->addSuccessMessage(__('You duplicated the post.'));
            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'post_id' => $newPost->getId(),
                    '_current' => true
                ]
            );
        }
        $this->dataPersistor->clear('today_deals_post');
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $model->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check exist identifier
     * @param $model
     * @param $identifier
     * @param $postId
     * @return bool
     */
    private function isUrlKeyExist($model, $identifier, $postId)
    {
        $result = $model->getCollection()
            ->addFieldToFilter('identifier', $identifier)
            ->addFieldToFilter('post_id', ['neq' => $postId]);
        if ($result->getSize() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $data
     * @param string $name
     * @param string $path
     */
    protected function filterDataImage(&$data, $name, $path)
    {
        $rawData = $data;
        if (isset($data[$name]) && is_array($data[$name])) {
            if (!empty($data[$name]['delete'])) {
                $data[$name] = null;
                $data[$path] = null;
            } else {
                if (isset($rawData[$name][0]['name'])) {
                    $data[$name] = $rawData[$name][0]['name'];
                }
                if (isset($rawData[$name][0]['path'])) {
                    $data[$path] = $rawData[$name][0]['path'];
                } elseif (isset($rawData[$name][0]['url'])) {
                    $rawPath = explode('/', $rawData[$name][0]['url']);
                    unset($rawPath[0]);
                    unset($rawPath[1]);
                    $data[$path] = implode('/', $rawPath);
                }
            }
        }

        if (!isset($data[$name])) {
            $data[$name] = '';
        }
    }

    /**
     * @param array $data
     */
    protected function filterDataProduct(&$data)
    {
        if (isset($data['todaydeal_post_form_product_listing'])) {
            $productIds = [];
            foreach ($data['todaydeal_post_form_product_listing'] as $item) {
                $productIds[] = $item['entity_id'];
            }

            $products = $data['links']['products'];
            foreach ($products as $product) {
                $data['product_position'][] = $product['position'];
            }

            $data['product_ids'] = $productIds;
            if (count($productIds) % 2 != 0) {
                array_pop($data['product_ids']);
                array_pop($data['product_position']);
            }
        } else {
            $data['product_ids'] = [];
            $data['product_position'] = [];
        }
    }

    /**
     * @param array $data
     */
    protected function filterDataVideo(&$data)
    {
        $rawData = $data;
        if (isset($data['file']) && is_array($data['file'])) {
            if (!empty($data['file']['delete'])) {
                $data['mb_video'] = null;
                $data['mb_video_path'] = null;
            } else {
                if (isset($rawData['file'][0]['name'])) {
                    $data['mb_video'] = $rawData['file'][0]['name'];
                }
                if (isset($rawData['file'][0]['path'])) {
                    $data['mb_video_path'] = $rawData['file'][0]['path'];
                }
            }
        }

        if (!isset($data['file'])) {
            $data['file'] = '';
        }
    }

    /**
     * @param array $data
     * @return boolean
     */
    protected function validatePeriodDate($data)
    {
        if (!$data['publish_from'] || !$data['publish_to']) {
            return true;
        }
        return $data['publish_from'] <= $data['publish_to'];
    }
}

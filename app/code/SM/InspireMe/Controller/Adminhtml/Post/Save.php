<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Controller\Adminhtml\Post;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Mirasvit\Blog\Api\Data\PostInterface;
use Mirasvit\Blog\Api\Repository\PostRepositoryInterface;
use Mirasvit\Blog\Api\Repository\TagRepositoryInterface;
use Mirasvit\Blog\Model\Post;
use SM\InspireMe\Helper\Data;

/**
 * Class Save
 * @package SM\InspireMe\Controller\Adminhtml\Post
 */
class Save extends \Mirasvit\Blog\Controller\Adminhtml\Post\Save
{
    /**
     * @var \Mirasvit\Blog\Helper\Category
     */
    protected $categoryHelper;

    /**
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * Save constructor.
     * @param \Mirasvit\Blog\Helper\Category $categoryHelper
     * @param TagRepositoryInterface $tagRepository
     * @param JsonFactory $jsonFactory
     * @param PostRepositoryInterface $postRepository
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        \Mirasvit\Blog\Helper\Category $categoryHelper,
        TagRepositoryInterface $tagRepository,
        JsonFactory $jsonFactory,
        PostRepositoryInterface $postRepository,
        Registry $registry,
        Context $context
    ) {
        parent::__construct($tagRepository, $jsonFactory, $postRepository, $registry, $context);
        $this->tagRepository = $tagRepository;
        $this->categoryHelper = $categoryHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(PostInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->filterPostData($this->getRequest()->getParams());
        if ($data) {
            /** @var Post $model */
            $model = $this->initModel();
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This article no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }
            $model->addData($data);

            if (!$data['is_short_content']) {
                $model->setShortContent('');
            }

            try {
                $this->postRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the article.'));
                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', [PostInterface::ID => $model->getId()]);
                }

                return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [PostInterface::ID => $this->getRequest()->getParam(PostInterface::ID)]
                );
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage(__('No data to save.'));

            return $resultRedirect;
        }
    }

    /**
     * @param array $rawData
     *
     * @return array
     */
    private function filterPostData(array $rawData)
    {
        $data = $rawData;

        $this->filterDataImage($data, PostInterface::FEATURED_IMAGE);
        $this->filterDataImage($data, Data::POST_DATA_HOME_IMAGE);
        $this->filterDataProduct($data);
        $this->filterDataPositionHomepage($data);
        $this->filterDataTag($data);

        $data['category_ids'][] = $this->categoryHelper->getRootCategory()->getId();

        return $data;
    }

    /**
     * @param array $data
     */
    protected function filterDataProduct(&$data)
    {
        if (isset($data['blog_post_form_product_listing'])) {
            $productIds = [];
            foreach ($data['blog_post_form_product_listing'] as $item) {
                $productIds[] = $item['entity_id'];
            }
            $data[PostInterface::PRODUCT_IDS] = array_slice($productIds, 0, 5, true);

            $products = $data['links']['products'];
            foreach ($products as $product) {
                $data[Data::RELATED_PRODUCT_POSITION][] = $product['position'];
                $data[Data::RELATED_PRODUCT_VALUE][]    = $product['value'];
            }
        } else {
            $data[PostInterface::PRODUCT_IDS] = [];
            $data[Data::RELATED_PRODUCT_POSITION] = [];
            $data[Data::RELATED_PRODUCT_VALUE] = [];
        }
    }

    /**
     * @param array $data
     * @param string $type
     */
    protected function filterDataImage(&$data, $type)
    {
        if (isset($data[$type]) && is_array($data[$type])) {
            if (!empty($data[$type]['delete'])) {
                $data[$type] = null;
            } else {
                if (isset($data[$type][0]['name'])) {
                    $data[$type] = $data[$type][0]['name'];
                }
            }
        }

        if (!isset($data[$type])) {
            $data[$type] = '';
        }
    }

    /**
     * @param array $data
     */
    protected function filterDataPositionHomepage(&$data)
    {
        if (isset($data['position_homepage'])) {
            $data['position'] = $data['position_homepage'];
            unset($data['position_homepage']);

            /** @var Post $post */
            $post = $this->postRepository->getCollection()
                ->addFieldToFilter('position', ['eq' => $data['position']])
                ->getFirstItem();

            if ($post->getId()) {
                $post->setPosition(null);
                $this->postRepository->save($post);
            }
        }
    }

    /**
     * @param array $data
     */
    protected function filterDataTag(&$data)
    {
        if (isset($data[PostInterface::TAG_IDS]) && is_array($data[PostInterface::TAG_IDS])) {
            $data[PostInterface::TAG_IDS] = array_slice($data[PostInterface::TAG_IDS], 0, 5);
            foreach ($data[PostInterface::TAG_IDS] as $idx => $tagId) {
                if (!is_numeric($tagId)) {
                    $tag = $this->tagRepository->create()->setName($tagId);
                    $tag = $this->tagRepository->ensure($tag);
                    $data[PostInterface::TAG_IDS][$idx] = $tag->getId();
                }
            }
        } else {
            $data[PostInterface::TAG_IDS] = [null];
        }
    }
}

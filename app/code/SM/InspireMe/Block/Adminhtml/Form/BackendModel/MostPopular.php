<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Adminhtml\Form\BackendModel;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Mirasvit\Blog\Api\Repository\PostRepositoryInterface;
use SM\InspireMe\Helper\Data;

/**
 * Class MostPopular
 * @package SM\InspireMe\Block\Adminhtml\Form\BackendModel
 */
class MostPopular extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var Json|null
     */
    private $serializer;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * MostPopular constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param PostRepositoryInterface $postRepository
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Mirasvit\Blog\Api\Repository\PostRepositoryInterface $postRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        Json $serializer = null
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data, $serializer);
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        $this->messageManager = $messageManager;
        $this->postRepository = $postRepository;
    }

    /**
     * @return \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (!is_array($value)) {
            $value = $this->serializer->unserialize($value);
        }

        for ($row = 1; $row <= 3; $row++) {
            if ($value[$row][Data::MP_BASED_ON]) {
                if (!$this->validateInt($value[$row][Data::MP_SELECT_ARTICLE_ID])) {
                    $this->setValue($this->getOldValue());
                    $this->messageManager->addErrorMessage(__('Wrong Input Article ID'));
                    return parent::beforeSave();
                } else {
                    $post = $this->postRepository->get($value[$row][Data::MP_SELECT_ARTICLE_ID]);
                    if ($post && $post->getId()) {
                        $value[$row][Data::MP_SELECT_ARTICLE] = $post->getName();
                    } else {
                        $value[$row][Data::MP_SELECT_ARTICLE] = null;
                    }
                }
            }
        }

        for ($row = 1; $row <= 3; $row++) {
            $value[$row][Data::MP_POSITION] = "Position " . $row;
        }

        $value = $this->serializer->serialize($value);
        $this->setValue($value);

        return parent::beforeSave();
    }

    /**
     * @param $data
     * @return bool
     */
    protected function validateInt($data)
    {
        return $data && is_numeric($data);
    }
}

<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 * @package SM\Help\Model
 */
class Config
{
    const MEDIA_FOLDER = 'transmart/thumbnail';

    const CONFIG_BASE_ROUTE          = 'sm_help/seo/base_route';
    const CONFIG_TOPIC_URL_SUFFIX    = 'sm_help/seo/topic_url_suffix';
    const CONFIG_QUESTION_URL_SUFFIX = 'sm_help/seo/topic_url_suffix';

    const CONFIG_TOP_QUESTION = 'sm_help/main_page/top_faq';
    const CONFIG_CONTACT_US   = 'sm_help/main_page/contact_us_block';
    const CONFIG_TOPIC_SHOW_TAB_RETURN = 'sm_help/main_page/topic_show_tab';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Filesystem $filesystem
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->url = $url;
        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->url->getUrl($this->getBaseRoute());
    }

    /**
     * @return string
     */
    public function getBaseRoute()
    {
        return $this->scopeConfig->getValue(self::CONFIG_BASE_ROUTE);
    }

    /**
     * @param null|string $store
     *
     * @return string
     */
    public function getTopicUrlSuffix($store = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_TOPIC_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return string
     */
    public function getQuestionUrlSuffix($store = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_QUESTION_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|string $store
     * @return mixed
     */
    public function getTopQuestion($store = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_TOP_QUESTION,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|string $store
     * @return mixed
     */
    public function getTopicShowTabReturn($store = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_TOPIC_SHOW_TAB_RETURN,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|int|string $store
     * @return mixed
     */
    public function getContactUsBlock($store = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_CONTACT_US,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $image
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMediaPath($image)
    {
        $path = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath() . self::MEDIA_FOLDER;

        if (!file_exists($path) || !is_dir($path)) {
            $this->filesystem
                ->getDirectoryWrite(DirectoryList::MEDIA)
                ->create($path);
        }

        return $path . '/' . $image;
    }

    /**
     * @param string $image
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl($image)
    {
        if (!$image) {
            return false;
        }

        $url = $this->storeManager->getStore()
                ->getBaseUrl(MagentoUrlInterface::URL_TYPE_MEDIA) . self::MEDIA_FOLDER;

        $url .= '/' . $image;

        return $url;
    }

}

<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Plugin\Mirasvit\Blog\Ui\Component\Listing\Column;

use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Mirasvit\Blog\Api\Data\PostInterface;

/**
 * Class PostActions
 * @package SM\InspireMe\Plugin\Mirasvit\Blog\Ui\Component\Listing\Column
 */
class PostActions extends \Mirasvit\Blog\Ui\Component\Listing\Column\PostActions
{
    /** Url path */
    const POST_URL_PATH_DELETE = 'blog/post/delete';

    /**
     * @param \Mirasvit\Blog\Ui\Component\Listing\Column\PostActions $subject
     * @param array $dataSource
     * @return mixed
     */
    public function afterPrepareDataSource(
        \Mirasvit\Blog\Ui\Component\Listing\Column\PostActions $subject,
        $dataSource
    ) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $subject->getData('name');
                if (isset($item[PostInterface::ID])) {
                    $item[$name]['delete'] = [
                        'href'  => $subject->urlBuilder->getUrl(self::POST_URL_PATH_DELETE, [
                            PostInterface::ID => $item[PostInterface::ID],
                        ]),
                        'label' => __('Delete'),
                    ];
                }
            }
        }

        return $dataSource;
    }
}

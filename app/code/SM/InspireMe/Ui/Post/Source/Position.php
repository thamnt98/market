<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Ui\Post\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\Blog\Api\Repository\PostRepositoryInterface;

/**
 * Class Position
 * @package SM\InspireMe\Ui\Post\Source
 */
class Position implements OptionSourceInterface
{
    const POST_POSITION = 'position';

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * Position constructor.
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(
        PostRepositoryInterface $postRepository
    ) {
        $this->postRepository = $postRepository;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $result = [];

        for ($position = 1; $position <= 5; $position++) {
            $result[] = [
                'value' => 6 - $position,
                'label' => __('%1. Current: Empty', $position)
            ];
        }

        $collection = $this->postRepository->getCollection()
            ->addFieldToFilter(self::POST_POSITION, ['neq' => null])
            ->setOrder(self::POST_POSITION, 'ASC');

        /** @var \Mirasvit\Blog\Model\Post $post */
        foreach ($collection as $post) {
            $position = $post->getPosition();
            $result[5 - $position] = [
                'value' => $position,
                'label' => __('%1. Current: %2', 6 - $position, $post->getName())
            ];
        }

        return $result;
    }
}

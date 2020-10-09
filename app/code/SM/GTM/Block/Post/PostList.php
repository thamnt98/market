<?php


namespace SM\GTM\Block\Post;
use SM\InspireMe\Helper\Data;

class PostList implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * PostList constructor.
     * @param Data $dataHelper
     */
    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param $post
     *
     * @return string
     */
    public function getGtm($post)
    {
        return $this->dataHelper->prepareGtmData($post);
    }
}

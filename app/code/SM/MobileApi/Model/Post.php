<?php


namespace SM\MobileApi\Model;


class Post extends \Mirasvit\Blog\Model\Post
{
    /**
     * @return string
     */
    public function getArticleList()
    {
        return __("Most Popular Articles");
    }
}

<?php

namespace SM\Checkout\Plugin\AmastyBannersLite\Model\SalesRule;

class SaveHandler
{
    /**
     * @param \Amasty\BannersLite\Model\SalesRule\SaveHandler $subject
     * @param \Closure $proceed
     * @param $entity
     * @param array $arguments
     * @return mixed
     */
    public function aroundExecute(
        \Amasty\BannersLite\Model\SalesRule\SaveHandler $subject,
        \Closure $proceed,
        $entity,
        $arguments = []
    ) {
        return $entity;
    }
}

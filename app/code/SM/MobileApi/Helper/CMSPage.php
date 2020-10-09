<?php

namespace SM\MobileApi\Helper;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Data
 * @package SM\MobileApi\Helper
 */
class CMSPage extends Data
{
    const TERM_AND_CONDITIONS = 'sm_mobile/cms_page/terms_conditions';
    const PRIVACY_AND_POLICY = 'sm_mobile/cms_page/privacy_policy';
    const ABOUT_US = 'sm_mobile/cms_page/about_us';

    /**
     * @return int|string
     * @throws NoSuchEntityException
     */
    public function getPolicyPageId()
    {
        return $this->getConfigValue(self::PRIVACY_AND_POLICY) ?? 1;
    }

    /**
     * @return int|string
     * @throws NoSuchEntityException
     */
    public function getTermsPageId()
    {
        return $this->getConfigValue(self::TERM_AND_CONDITIONS) ?? 1;
    }

    /**
     * @return int|string
     * @throws NoSuchEntityException
     */
    public function getAboutUsId(){
        return $this->getConfigValue(self::ABOUT_US) ?? 1;
    }

    /**
     * @return int|string
     * @throws NoSuchEntityException
     */
    public function getPolicyPageIdWeb()
    {
        return $this->getConfigValue(self::PRIVACY_AND_POLICY) ?? null;
    }

    /**
     * @return int|string
     * @throws NoSuchEntityException
     */
    public function getTermsPageIdWeb()
    {
        return $this->getConfigValue(self::TERM_AND_CONDITIONS) ?? null;
    }

    /**
     * @return null|string
     * @throws NoSuchEntityException
     */
    public function getAboutUsIdWeb(){
        return $this->getConfigValue(self::ABOUT_US) ?? null;

    }
}

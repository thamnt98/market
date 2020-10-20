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

namespace SM\TodayDeal\Api\Data;

interface PostInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const POST_ID                  = 'post_id';
    const IDENTIFIER               = 'identifier';
    const TITLE                    = 'title';
    const TODAY_DEAL_LAYOUT        = 'today_deal_layout';
    const META_TITLE               = 'meta_title';
    const META_KEYWORDS            = 'meta_keywords';
    const META_DESCRIPTION         = 'meta_description';
    const CONTENT_HEADING          = 'content_heading';
    const CONTENT                  = 'content';
    const CREATION_TIME            = 'creation_time';
    const UPDATE_TIME              = 'update_time';
    const SORT_ORDER               = 'sort_order';
    const LAYOUT_UPDATE_XML        = 'layout_update_xml';
    const CUSTOM_THEME             = 'custom_theme';
    const CUSTOM_ROOT_TEMPLATE     = 'custom_root_template';
    const CUSTOM_LAYOUT_UPDATE_XML = 'custom_layout_update_xml';
    const CUSTOM_THEME_FROM        = 'custom_theme_from';
    const CUSTOM_THEME_TO          = 'custom_theme_to';
    const IS_ACTIVE                = 'is_active';
    const THUMBNAIL_NAME           = 'thumbnail_name';
    const THUMBNAIL_PATH           = 'thumbnail_path';
    const THUMBNAIL_SIZE           = 'thumbnail_size';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get page layout
     *
     * @return string|null
     */
    public function getTodayDealLayout();

    /**
     * Get meta title
     *
     * @return string|null
     * @since 101.0.0
     */
    public function getMetaTitle();

    /**
     * Get meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Get content heading
     *
     * @return string|null
     */
    public function getContentHeading();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Get layout update xml
     *
     * @return string|null
     * @deprecated 103.0.4 Existing updates are applied, new are not accepted.
     */
    public function getLayoutUpdateXml();

    /**
     * Get custom theme
     *
     * @return string|null
     */
    public function getCustomTheme();

    /**
     * Get custom root template
     *
     * @return string|null
     */
    public function getCustomRootTemplate();

    /**
     * Get custom layout update xml
     *
     * @deprecated 103.0.4 Existing updates are applied, new are not accepted.
     * @see \Magento\Cms\Model\Page\CustomLayout\Data\CustomLayoutSelectedInterface
     * @return string|null
     */
    public function getCustomLayoutUpdateXml();

    /**
     * Get custom theme from
     *
     * @return string|null
     */
    public function getCustomThemeFrom();

    /**
     * Get custom theme to
     *
     * @return string|null
     */
    public function getCustomThemeTo();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Set ID
     *
     * @param int $id
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setId($id);

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setIdentifier($identifier);

    /**
     * Set title
     *
     * @param string $title
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setTitle($title);

    /**
     * Set page layout
     *
     * @param string $pageLayout
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setPageLayout($pageLayout);

    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return \SM\TodayDeal\Api\Data\PostInterface
     * @since 101.0.0
     */
    public function setMetaTitle($metaTitle);

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * Set content heading
     *
     * @param string $contentHeading
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setContentHeading($contentHeading);

    /**
     * Set content
     *
     * @param string $content
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setContent($content);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set sort order
     *
     * @param string $sortOrder
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * Set layout update xml
     *
     * @param string $layoutUpdateXml
     * @return \SM\TodayDeal\Api\Data\PostInterface
     * @deprecated 103.0.4 Existing updates are applied, new are not accepted.
     */
    public function setLayoutUpdateXml($layoutUpdateXml);

    /**
     * Set custom theme
     *
     * @param string $customTheme
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setCustomTheme($customTheme);

    /**
     * Set custom root template
     *
     * @param string $customRootTemplate
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setCustomRootTemplate($customRootTemplate);

    /**
     * Set custom layout update xml
     *
     * @param string $customLayoutUpdateXml
     * @return \SM\TodayDeal\Api\Data\PostInterface
     * @deprecated 103.0.4 Existing updates are applied, new are not accepted.
     * @see \Magento\Cms\Model\Page\CustomLayout\Data\CustomLayoutSelectedInterface
     */
    public function setCustomLayoutUpdateXml($customLayoutUpdateXml);

    /**
     * Set custom theme from
     *
     * @param string $customThemeFrom
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setCustomThemeFrom($customThemeFrom);

    /**
     * Set custom theme to
     *
     * @param string $customThemeTo
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setCustomThemeTo($customThemeTo);

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setIsActive($isActive);

    /**
     * Get thumbnail_name
     * @return string|null
     */
    public function getThumbnailName();

    /**
     * Set thumbnail_name
     * @param $thumbnailName
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setThumbnailName($thumbnailName);

    /**
     * Get thumbnail_path
     * @return string|null
     */
    public function getThumbnailPath();

    /**
     * Set thumbnail_path
     * @param string $thumbnailPath
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setThumbnailPath($thumbnailPath);

    /**
     * Get thumbnail_size
     * @return string|null
     */
    public function getThumbnailSize();

    /**
     * Set thumbnail_size
     * @param $thumbnailSize
     * @return \SM\TodayDeal\Api\Data\PostInterface
     */
    public function setThumbnailSize($thumbnailSize);
}

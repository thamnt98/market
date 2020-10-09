<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_VesMenu
 *
 * Date: March, 28 2020
 * Time: 8:16 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\VesMenu\Helper;

/**
 * Class Data
 *
 * @package SM\VesMenu\Helper
 */
class Data extends \Ves\Megamenu\Helper\Data
{
    const VALUE_YES = '1';
    const VALUE_NO = '0';
    /**
     * @Override
     *
     * Add GTM data to menu.
     *
     * @param array $item
     * @param int   $level
     * @param int   $x
     * @param bool  $listTag
     * @param int   $parentIndex
     * @param bool  $disable_mobile
     *
     * @return string
     */
    public function drawItem($item, $level = 0, $x = 1, $listTag = true, $parentIndex = 1, $disable_mobile = true)
    {
        static $rootMenu = [];
        $generate_mobile_menu_code = $this->getConfigMobileMenu();
        $html = "";
        try {
            $hasChildren = false;
            $class = $style = $attr = '';
            if (isset($item['class'])) {
                $class = $item['class'];
            }
            if (!isset($item['status']) || !$item['status']) {
                return '';
            }

            if ($level == 0) {
                $rootMenu = [];
            }

            if (($level < 3 || empty($item['children'])) && !empty($item['name'])) {
                $rootMenu[$level] = strip_tags($item['name']);
            }

            if ($this->isMobileDevice() && $this->isCheckMobileDevice()) {
                if ($item['content_type'] == 'dynamic') {
                    $item['content_type'] = 'childmenu';
                }
            }
            if (isset($item['children']) && count($item['children']) > 0) {
                $hasChildren = true;
            }

            $nav_class = "";
            if ($disable_mobile) {
                $nav_class = ' nav-' . $parentIndex;
            }
            $class .= ' nav-item level' . $level . $nav_class;
            // Item Align Type
            if ($item['align'] == '1') {
                $class .= ' submenu-left';
            } else {
                if ($item['align'] == '2') {
                    $class .= ' submenu-right';
                } else {
                    if ($item['align'] == '3') {
                        $class .= ' submenu-alignleft';
                    } else {
                        if ($item['align'] == '4') {
                            $class .= ' submenu-alignright';
                        }
                    }
                }
            }

            // Group Child Items
            if (is_null($item['is_group']) || $item['is_group']) {
                $class .= ' subgroup ';
            } else {
                $class .= ' subhover ';
            }

            if ($item['content_type'] == 'dynamic') {
                $class .= ' subdynamic';
            }

            // Disable dimension
            if (((int)$item['disable_bellow']) > 0) {
                $attr .= 'data-disable-bellow="' . $item['disable_bellow'] . '"';
            }

            if ($level == 0) {
                $class .= ' dropdown level-top';
            } else {
                $class .= ' dropdown-submenu';
            }
            $class .= ' ' . $item['classes'];

            // Custom Link, Category Link
            $href = '';
            if ($item['link_type'] == 'custom_link') {
                $href = $item['link'];
            } else {
                if ($item['link_type'] == 'category_link') {
                    if ($category = $this->getCategory($item['category'])) {
                        if ($disable_mobile) {
                            $class .= ' category-item';
                        }
                        $href = $category['url'];
                        if ($urls = parse_url($href)) {
                            $url_host = isset($urls['host']) ? $urls['host'] : "";
                            $base_url = $this->_storeManager->getStore()->getBaseUrl();
                            if ($url_host && ($base_urls = parse_url($base_url))) {
                                $base_urls['host'] = isset($base_urls['host']) ? $base_urls['host'] : "";
                                if ($url_host != $base_urls['host']) {
                                    $href = str_replace($url_host, $base_urls['host'], $href);
                                }
                            }
                        }
                    }
                }
            }

            if ($hasChildren) {
                $class .= ' parent';
            }

            if ($item['show_icon'] && $item['icon_position'] == 'left' && $item['icon'] != '') {
                $attr .= ' data-hovericon="' . $item['hover_icon'] . '" data-iconsrc="' . $item['icon'] . '"';
            }

            if (isset($item['caret']) && $item['caret']) {
                $attr .= ' data-hovercaret="' . $item['hover_caret'] . '" data-caret="' . $item['caret'] . '"';
            }

            if (isset($item['animation_in']) && $item['animation_in']) {
                $attr .= ' data-animation-in="' . $item['animation_in'] . '"';
            }

            if (isset($item['color']) && $item['color']) {
                $attr .= ' data-color="' . $item['color'] . '"';
            }

            if (isset($item['hover_color']) && $item['hover_color']) {
                $attr .= ' data-hover-color="' . $item['hover_color'] . '"';
            }

            if (isset($item['bg_color']) && $item['bg_color']) {
                $attr .= ' data-bgcolor="' . $item['bg_color'] . '"';
            }

            if (isset($item['bg_hover_color']) && $item['bg_hover_color']) {
                $attr .= ' data-hover-bgcolor="' . $item['bg_hover_color'] . '"';
            }

            if (!isset($item['htmlId'])) {
                $item['htmlId'] = time() . rand();
            }

            if ($listTag) {
                if ($class != '') {
                    $class = 'class="' . $class . '"';
                }

                $html = '<li id="' . $item['htmlId'] . '" ' . $class . ' ' . $style . ' ' . $attr . '>';
            } else {
                if (isset($item['dynamic'])) {
                    $class .= ' dynamic-item ' . $item['htmlId'];
                }

                if ($class != '') {
                    $class = 'class="' . $class . '"';
                }

                $html = '<div id="' . $item['htmlId'] . '" ' . $class . ' ' . $style . ' ' . $attr . '>';
            }

            if (isset($item['before_html']) && $item['before_html']) {
                $html .= '<div class="item-before-content">' . $item['before_html'] . '</div>';
            }

            if (!empty($rootMenu) && $level >= 0) {
                $item['gtm'] = $this->renderGtmData($rootMenu);
            }
            if (!isset($item['dynamic'])) {
                $html .= $this->drawAnchor($item, $level);
            }

            $tChildren = false;
            if ($item['content_type'] == 'parentcat') {
                $catChildren = $this->getTreeCategories($item['parentcat']);
                if ($catChildren) {
                    $tChildren = true;
                }
            }

            if (($item['show_footer'] && $item['footer_html'] != '') ||
                ($item['show_header'] && $item['header_html'] != '') ||
                ($item['show_left_sidebar'] && $item['left_sidebar_html'] != '') ||
                ($item['show_right_sidebar'] && $item['right_sidebar_html'] != '') ||
                (
                    $item['show_content'] &&
                    (
                        (
                            ($item['content_type'] == 'childmenu' || $item['content_type'] == 'dynamic') &&
                            (isset($item['children']) && count($item['children']) > 0)
                        ) || ($item['content_type'] == 'content' && $item['content_html'] != '')
                    )
                ) || ($item['content_type'] == 'parentcat' && $tChildren)
            ) {
                $level++;
                $subClass = $subStyle = $subAttr = '';

                if ($item['sub_width'] != '') {
                    $subStyle .= 'width:' . $item['sub_width'] . ';';
                    $subAttr .= 'data-width="' . $item['sub_width'] . '"';
                }

                if (isset($item['dropdown_bgcolor']) && $item['dropdown_bgcolor']) {
                    $subStyle .= 'background-color:' . $item['dropdown_bgcolor'] . ';';
                }

                if (isset($item['dropdown_bgimage']) && $item['dropdown_bgimage']) {
                    if (!$item['dropdown_bgpositionx']) {
                        $item['dropdown_bgpositionx'] = 'center';
                    }

                    if (!$item['dropdown_bgpositiony']) {
                        $item['dropdown_bgpositiony'] = 'center';
                    }

                    $dropdown_bgimage = $this->getImageLink($item['dropdown_bgimage']);
                    $subStyle .= 'background: url(\'' . $dropdown_bgimage . '\') ' . $item['dropdown_bgimagerepeat'] .
                        ' ' . $item['dropdown_bgpositionx'] . ' ' . $item['dropdown_bgpositiony'] .
                        ' ' . $item['dropdown_bgcolor'] . ';';
                }
                if (isset($item['dropdown_inlinecss']) && $item['dropdown_inlinecss']) {
                    $subStyle .= $item['dropdown_inlinecss'];
                }

                if (isset($item['dynamic'])) {
                    $subClass .= ' content-wrapper';
                }

                if (!isset($item['dynamic'])) {
                    $subClass .= ' submenu';
                    if ($item['is_group']) {
                        $subClass .= ' dropdown-mega';
                    } else {
                        $subClass .= ' dropdown-menu';
                    }
                }

                if (isset($item['animation_in'])) {
                    $subClass .= ' animated ';
                    $subClass .= $item['animation_in'];
                    if ($item['animation_in']) {
                        $subAttr .= ' data-animation-in="' . $item['animation_in'] . '"';
                    }
                    if ($item['animation_time']) {
                        $subStyle .= 'animation-duration: ' . $item['animation_time'] .
                            's;-webkit-animation-duration: ' . $item['animation_time'] . 's;';
                    }
                }

                if ($subClass != '') {
                    $subClass = 'class="' . $subClass . '"';
                }
                if ($subStyle != '') {
                    $subStyle = 'style="' . $subStyle . '"';
                }

                if (!isset($item['dynamic'])) {
                    $html .= '<div ' . $subClass . ' ' . $subAttr . ' ' . $subStyle . '>';
                }

                $html .= '<div class="drilldown-back"><a href="#"><span class="drill-opener"></span>' .
                    '<span class="current-cat"></span></a></div>' .
                    '<div class="submenu-inner">';

                // TOP BLOCK
                if ($item['show_header'] == self::VALUE_YES || !is_null($item['header_html'])) {
                    $html .= '<div class="item-header" data-gtm-event="seeAll_navigation_menu" data-gtm-name="category" ';
                    $html .= " data-gtm='{$item['gtm']}'>";
                    $html .= $this->decodeWidgets($item['header_html']);
                    $html .= '</div>';
                }

                if ($item['show_left_sidebar'] || $item['show_content'] || $item['show_right_sidebar']) {
                    if (!isset($item['dynamic'])) {
                        $html .= '<div class="content-wrapper">';
                    } else {
                        $html .= '<div ' . $subClass . ' ' . $subAttr . ' ' . $subStyle . '>';
                    }

                    $left_sidebar_width = isset($item['left_sidebar_width']) ? $item['left_sidebar_width'] : 0;
                    $content_width = $item['content_width'];
                    $right_sidebar_width = isset($item['right_sidebar_width']) ? $item['right_sidebar_width'] : 0;

                    // LEFT SIDEBAR BLOCK
                    if ($item['show_left_sidebar'] && $item['left_sidebar_html'] != '') {
                        if ($left_sidebar_width) {
                            $left_sidebar_width = 'style="width:' . $left_sidebar_width . '"';
                        }

                        $html .= '<div class="item-sidebar left-sidebar" ' . $left_sidebar_width . '>' .
                            $this->decodeWidgets($item['left_sidebar_html']) . '</div>';
                    }
                    // MAIN CONTENT BLOCK
                    if ($item['show_content'] &&
                        ((($item['content_type'] == 'childmenu' || $item['content_type'] == 'dynamic') && $hasChildren)
                            || $item['content_type'] == 'parentcat'
                            || ($item['content_type'] == 'content'
                                && $item['content_html'] != ''))
                    ) {
                        $html .= '<div class="item-content" ' .
                            ($content_width == '' ? '' : 'style="width:' . $content_width . '"') . '>';

                        // Content HTML
                        if ($item['content_type'] == 'content' && $item['content_html'] != '') {
                            $html .= '<div class="nav-dropdown">' . $this->decodeWidgets($item['content_html']) .
                                '</div>';
                        }

                        // Dynamic Tab
                        if ($item['content_type'] == 'dynamic' && $hasChildren) {
                            $column = (int)$item['child_col'];
                            $html .= '<div class="level' . $level . ' nav-dropdown ves-column' . $column . '">';
                            $sort_type = isset($item['submenu_sorttype']) ? $item['submenu_sorttype'] : 'normal';
                            $children = $this->sortMenuItems($item['children'], $sort_type);
                            $i = $z = 0;
                            $total = count($children);

                            if ((!$this->isMobileDevice() && $this->isCheckMobileDevice()) ||
                                !$this->isCheckMobileDevice()
                            ) {
                                $html .= '<div class="dorgin-items ' .
                                    (isset($item['tab_position']) ? 'dynamic-' . $item['tab_position'] : '') .
                                    ' row hidden-sm hidden-xs">';

                                $html .= '<div class="dynamic-items ';
                                if (!isset($item['tab_position']) ||
                                    (isset($item['tab_position']) && $item['tab_position'] != 'top')
                                ) {
                                    $html .= 'col-xs-3';
                                }

                                $html .= ' hidden-xs hidden-sm">';

                                $html .= '<ul>';
                                foreach ($children as $it) {
                                    $attr1 = '';
                                    if ($it['show_icon'] && $it['icon_position'] == 'left' && $it['icon'] != '') {
                                        $attr1 .= ' data-hovericon="' . $it['hover_icon'] . '" data-iconsrc="' .
                                            $it['icon'] . '"';
                                    }

                                    if ($it['caret']) {
                                        $attr1 .= ' data-hovercaret="' . $it['hover_caret'] . '" data-caret="' .
                                            $it['caret'] . '"';
                                    }

                                    $iClass = 'nav-item';
                                    if ($z == 0) {
                                        $iClass .= ' dynamic-active';
                                    }
                                    if ($iClass) {
                                        $iClass = 'class="' . $iClass . '"';
                                    }
                                    $html .= '<li ' . $iClass . ' data-dynamic-id="' . $it['htmlId'] .
                                        '" ' . $attr1 . '>';
                                    $html .= $this->drawAnchor($it, $level);
                                    $html .= '</li>';
                                    $i++;
                                    $z++;
                                }

                                $html .= '</ul>';
                                $html .= '</div>';
                                $html .= '<div class="dynamic-content ';
                                if (!isset($item['tab_position']) ||
                                    (isset($item['tab_position']) && $item['tab_position'] != 'top')
                                ) {
                                    $html .= 'col-xs-9';
                                }
                                $html .= ' hidden-xs hidden-sm">';

                                $z = 0;
                                foreach ($children as $it) {
                                    if ($z == 0) {
                                        $it['class'] = 'dynamic-active';
                                    }
                                    $it['dynamic'] = true;
                                    $html .= $this->filter(
                                        $this->drawItem($it, $level, $i, false, $parentIndex, $disable_mobile)
                                    );
                                    $i++;
                                    $z++;
                                }
                                $html .= '</div>';
                                $html .= '</div>';
                            }
                            if (($this->isMobileDevice() && $this->isCheckMobileDevice()) ||
                                !$this->isCheckMobileDevice()
                            ) {
                                $html .= '<div class="orgin-items hidden-lg hidden-md">';
                                $i = 0;
                                $column = 1;
                                foreach ($children as $it) {
                                    $html .= '<div class="mega-col col-sm-' . (12 / $column) . ' mega-col-' . $i .
                                        ' mega-col-level-' . $level . '">';
                                    $html .= $this->filter(
                                        $this->drawItem($it, $level, $i, false, $parentIndex, $disable_mobile)
                                    );
                                    $html .= '</div>';
                                    $i++;
                                }
                                $html .= '</div>';
                            }

                            $html .= '</div>';
                        }
                        // Child item
                        if (($item['content_type'] == 'childmenu' && $hasChildren) ||
                            $item['content_type'] == 'parentcat'
                        ) {
                            $column = (int)$item['child_col'];
                            $grid_type = isset($item['child_col_type']) ? $item['child_col_type'] : "normal";//bootstrap|normal
                            $column_size = 12;
                            if ($grid_type == "bootstrap" && $column > 1) {
                                $column_size = 12 / $column;
                            }
                            $custom_class = "";

                            $sort_type = isset($item['submenu_sorttype']) ? $item['submenu_sorttype'] : 'normal';

                            if ($item['content_type'] == 'parentcat') {
                                $isgroup_level = isset($item['isgroup_level']) ? (int)$item['isgroup_level'] : 0;
                                $custom_class = 'content-type-parentcat';
                                $it_level = 1;
                                $list = [];
                                $max_level = 100;
                                $catChildren = $this->getTreeCategories(
                                    $item['parentcat'],
                                    $it_level,
                                    $list,
                                    $max_level,
                                    $isgroup_level,
                                    $sort_type
                                );
                                $children = $this->sortMenuItems($catChildren, $sort_type);
                            } else {
                                $children = $this->sortMenuItems($item['children'], $sort_type);
                            }

                            $html .= '<div class="level' . $level . ' nav-dropdown ves-column' .
                                $column . ' ' . $custom_class . '">';

                            $i = 0;
                            $total = count($children);
                            $resultTmp = [];
                            $x1 = 0;
                            $levelTmp = 1;

                            $resultTmpSort = [];
                            $childIndex = 1;
                            foreach ($children as $z => $it) {
                                if ($grid_type == "bootstrap") {
                                    $parentChildIndex = $parentIndex . "-" . $childIndex;
                                    $resultTmp[] = $this->drawItem(
                                        $it,
                                        $level,
                                        $i,
                                        false,
                                        $parentChildIndex,
                                        $disable_mobile
                                    );
                                    $i++;
                                    $childIndex++;
                                } else {
                                    //$resultTmp[$x1][$levelTmp] = $this->drawItem($it, $level, $i, false);
                                    $resultTmpSort[$x1][$levelTmp] = 1;
                                    if ($x1 == $column - 1 || $i == (count($children) - 1)) {
                                        $levelTmp++;
                                        $x1 = 0;
                                    } else {
                                        $x1++;
                                    }
                                    $i++;
                                }
                            }

                            if ($resultTmpSort) {
                                $index2 = 0;
                                $childIndex = 1;
                                foreach ($resultTmpSort as $_k2 => $_v2) {
                                    if ($_v2) {
                                        $index3 = 0;
                                        foreach ($_v2 as $_k3 => $_v3) {
                                            if (isset($children[$index2])) {
                                                $parentChildIndex = $parentIndex . "-" . $childIndex;
                                                $resultTmp[$_k2][$index3] = $this->drawItem(
                                                    $children[$index2],
                                                    $level,
                                                    $_k3,
                                                    false,
                                                    $parentChildIndex,
                                                    $disable_mobile
                                                );
                                                $index3++;
                                                $childIndex++;
                                            }
                                            $index2++;
                                        }
                                    }
                                }
                            }

                            if ($generate_mobile_menu_code) {
                                $html .= '<div class="item-content1 ' . self::$_hidden_menu_content_1 . '">';
                            } else {
                                $html .= '<div class="item-content1">';
                            }
                            $i2 = $i3 = 0;
                            foreach ($resultTmp as $k1 => $v1) {
                                if ($grid_type == "bootstrap") {
                                    $i2++;
                                    $i3++;
                                    switch ($column) {
                                        case 5:
                                            if ($i3 <= 3) {
                                                $column_size = 3;
                                            } elseif ($i3 == 4) {
                                                $column_size = 2;
                                            } elseif ($i3 == 5) {
                                                $column_size = 1;
                                            }
                                            break;
                                        case 7:
                                            if ($i3 <= 5) {
                                                $column_size = 2;
                                            } else {
                                                $column_size = 1;
                                            }
                                            break;
                                        case 8:
                                            if ($i3 <= 4) {
                                                $column_size = 2;
                                            } else {
                                                $column_size = 1;
                                            }
                                            break;
                                        case 9:
                                            if ($i3 <= 3) {
                                                $column_size = 2;
                                            } else {
                                                $column_size = 1;
                                            }
                                            break;
                                        case 10:
                                            if ($i3 <= 2) {
                                                $column_size = 2;
                                            } else {
                                                $column_size = 1;
                                            }
                                            break;
                                        case 11:
                                            if ($i3 <= 1) {
                                                $column_size = 2;
                                            } else {
                                                $column_size = 1;
                                            }
                                            break;
                                    }
                                    if ($i2 == 1 || ($i2 - 1) % $column == 0) {
                                        $html .= '<div class="mega-row row">';
                                    }

                                    $html .= '<div class="mega-col mega-col-' . $column_size . ' col-sm-' .
                                        $column_size . ' mega-col-level-' . $level . ' col-xs-12">';
                                    $html .= $v1;
                                    $html .= '</div>';

                                    if ($i2 % $column == 0 || $i2 == count($resultTmp)) {
                                        $html .= '</div>';
                                        $i3 = 0;
                                    }
                                } else {
                                    $html .= '<div class="mega-col mega-col-' . $i . ' mega-col-level-' .
                                        $level . ' col-xs-12">';
                                    foreach ($v1 as $k2 => $v2) {
                                        $html .= $v2;
                                    }
                                    $html .= '</div>';
                                }
                            }
                            $html .= '</div>';
                            if ($generate_mobile_menu_code) {
                                $html .= '<div class="item-content2 ' . self::$_hidden_menu_content_2 . '">';
                                foreach ($children as $z => $it) {
                                    $it['htmlId'] = isset($it['htmlId']) ? ($it['htmlId'] . '2') : (time() . rand());
                                    $html .= $this->filter(
                                        $this->drawItem($it, $level, $i, false, $parentIndex, false)
                                    );
                                }
                                $html .= '</div>';
                            }
                            $html .= '</div>';
                        }

                        $html .= '</div>';
                    }

                    // RIGHT SIDEBAR BLOCK
                    if ($item['show_right_sidebar'] == self::VALUE_YES || !is_null($item['right_sidebar_html'])) {
                        if ($right_sidebar_width) {
                            $right_sidebar_width = 'style="width:' . $right_sidebar_width . '"';
                        }
                        $html .= '<div class="item-sidebar right-sidebar" ' . $right_sidebar_width . '>' .
                            $this->decodeWidgets($item['right_sidebar_html']) . '</div>';
                    }

                    $html .= '</div>';
                }

                // BOOTM BLOCK
                if ($item['show_footer'] && $item['footer_html'] != '') {
                    $html .= '<div class="item-footer">' . $this->decodeWidgets($item['footer_html']) . '</div>';
                }

                $html .= '</div>';

                if (!isset($item['dynamic'])) {
                    $html .= '</div>';
                }
            }

            if (isset($item['after_html']) && $item['after_html']) {
                $html .= '<div class="item-after-content">' . $item['after_html'] . '</div>';
            }

            if ($listTag) {
                $html .= '</li>';
            } else {
                $html .= '</div>';
            }
        } catch (\Exception $e) {
        }

        return $html;
    }

    /**
     * @Override
     *
     * Add GTM data to menu link.
     *
     * @param array $item
     * @param int   $level
     * @param bool  $return_link
     *
     * @return array|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function drawAnchor($item, $level = 1, $return_link = false)
    {
        $hasChildren = false;
        $tChildren = false;
        if ($this->isMobileDevice() && $this->isCheckMobileDevice()) {
            if ($item['content_type'] == 'dynamic') {
                $item['content_type'] = 'childmenu';
            }
        }

        if ($item['content_type'] == 'parentcat') {
            $catChildren = $this->getTreeCategories($item['parentcat']);
            if ($catChildren) {
                $tChildren = true;
            }
        }

        if (($item['show_footer'] && $item['footer_html'] != '') ||
            ($item['show_header'] && $item['header_html'] != '') ||
            ($item['show_left_sidebar'] && $item['left_sidebar_html'] != '') ||
            ($item['show_right_sidebar'] && $item['right_sidebar_html'] != '') ||
            (
                $item['show_content'] &&
                (
                    (
                        ($item['content_type'] == 'childmenu' || $item['content_type'] == 'dynamic') &&
                        (isset($item['children']) && count($item['children']) > 0)
                    ) || ($item['content_type'] == 'content' && $item['content_html'] != '')
                )
            ) || ($item['content_type'] == 'parentcat' && $tChildren)
        ) {
            $hasChildren = true;
        }

        $html = $class = $style = $attr = '';
        // Design
        if (isset($item['color']) && $item['color'] != '') {
            $style .= 'color: ' . $item['color'] . ';';
        }

        if (isset($item['bg_color']) && $item['bg_color'] != '') {
            $style .= 'background-color: ' . $item['bg_color'] . ';';
        }

        if (isset($item['inline_css']) && $item['inline_css'] != '') {
            $style .= $item['inline_css'];
        }

        if ($style != '') {
            $style = 'style="' . $style . '"';
        }

        $class .= ' nav-anchor';
        if ($item['content_type'] == 'dynamic') {
            $class .= ' subdynamic';
        }

        if ($item['is_group']) {
            $class .= ' subitems-group';
        }

        // Custom Link, Category Link
        $href = '';
        if ($item['link_type'] == 'custom_link') {
            $href = $this->filter($item['link']);
            if ($this->endsWith($href, '/')) {
                $href = substr_replace($href, "", -1);
            }
        } else {
            if ($item['link_type'] == 'category_link') {
                if ($category = $this->getCategory($item['category'])) {
                    $href = $category['url'];
                    if ($urls = parse_url($href)) {
                        $url_host = isset($urls['host']) ? $urls['host'] : "";
                        $base_url = $this->_storeManager->getStore()->getBaseUrl();
                        if ($url_host && ($base_urls = parse_url($base_url))) {
                            $base_urls['host'] = isset($base_urls['host']) ? $base_urls['host'] : "";
                            if ($url_host != $base_urls['host']) {
                                $href = str_replace($url_host, $base_urls['host'], $href);
                            }
                        }
                    }
                }
            }
        }

        if ($return_link) { //If return link item, will return item, else return html code
            $item['href'] = $href;

            return $item;
        }

        if ($class != '') {
            $class = 'class="' . $class . '"';
        }

        // Attributes
        if (isset($item['hover_color']) && $item['hover_color']) {
            $attr .= ' data-hover-color="' . $item['hover_color'] . '"';
        }

        if (isset($item['bg_hover_color']) && $item['bg_hover_color']) {
            $attr .= ' data-hover-bgcolor="' . $item['bg_hover_color'] . '"';
        }

        if (isset($item['color']) && $item['color']) {
            $attr .= ' data-color="' . $item['color'] . '"';
        }

        if (isset($item['bg_color']) && $item['bg_color']) {
            $attr .= ' data-bgcolor="' . $item['bg_color'] . '"';
        }

        if (!empty($item['gtm'])) {
            $attr .= " data-gtm='{$item['gtm']}'";
            $attr .= " data-gtm-name='category'";
            if ($level == 1) {
                $attr .= ' data-gtm-event="categories_click"';
            } elseif ($level >= 0 || $level > 1) {
                $attr .= ' data-gtm-event="navigation_menu"';
            }
        }

        $target = $item['target'] ? 'target="' . $item['target'] . '"' : '';
        if ($href == '') {
            $href = '#';
        }
        if ($href == '#') {
            $target = '';
        }

        if ($item['name'] != '' || $item['icon']) {
            $html .= '<a href="' . $href . '" title="' . strip_tags($item['name']) . '" ' .
                $target . ' ' . $attr . ' ' . $style . ' ' . $class . '>';
        }

        if ($item['show_icon'] && $item['icon_classes'] != '') {
            $html .= '<i class="' . $item['icon_classes'] . '"></i>';
        }

        // Icon Left
        if ($item['show_icon'] && $item['icon_position'] == 'left' && $item['icon'] != '') {
            $html .= '<img class="item-icon icon-left" ';
            if ($item['hover_icon']) {
                $html .= ' data-hoverimg="' . $item['hover_icon'] . '"';
            }

            $html .= ' src="' . $item['icon'] . '" alt="' . strip_tags($item['name']) . '"/>';
        }

        if ($item['name'] != '') {
            $html .= '<span>' . $item['name'] . '</span>';
        }

        // Icon Right
        if ($item['show_icon'] && $item['icon_position'] == 'right' && $item['icon'] != '') {
            $html .= '<img class="item-icon icon-right" ';
            if ($item['hover_icon']) {
                $html .= ' data-hoverimg="' . $item['hover_icon'] . '"';
            }

            $html .= ' src="' . $item['icon'] . '" alt="' . strip_tags($item['name']) . '"/>';
        }

        if (isset($item['caret']) && $item['caret']) {
            $html .= '<i class="ves-caret fa ' . $item['caret'] . '"></i>';
        }

        if ($hasChildren) {
            $html .= '<span class="opener"></span>';
        }

        if ($hasChildren) {
            $html .= '<span class="drill-opener"></span>';
        }

        if ($item['name'] != '') {
            $html .= '</a>';
        }

        return $html;
    }

    /**
     * Generate GTM menu data.
     *
     * @param array $data
     *
     * @return string
     */
    protected function renderGtmData($data)
    {
        if (empty($data) || !is_array($data)) {
            return '';
        }

        $data = array_values($data);

        if (array_key_exists('2', $data) && array_key_exists('3', $data)) {
            if ($data[2] == $data[3]) {
                $data[3] = 'Not available';
            }
        }

        if (array_key_exists('0', $data)) {
            if (!array_key_exists('1', $data)) {
                $data[1] = $data[0];
            }
        }
        return \Zend_Json_Encoder::encode([
            'menu_category' => $data[1] ?? 'Not available',
            'menu_name'     => $data[2] ?? 'Not available',
            'submenu_name'  => $data[3] ?? 'Not available'
        ], true);
    }
}

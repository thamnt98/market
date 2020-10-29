<?php
/**
 * Class CartProductInteface
 * @package SM\Checkout\Api\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Api\Data\CartItem;

interface InstallationInterface
{
    const ALLOW_INSTALLATION = 'allow_installation';
    const TOOLTIP            = 'tooltip_message';

    /**
     * @return string
     */
    public function getIsInstallation();

    /**
     * @return string
     */
    public function getInstallationNote();

    /**
     * @return string
     */
    public function getInstallationFee();

    /**
     * @param int $data
     * @return $this
     */
    public function setIsInstallation($data);

    /**
     * @param string $data
     * @return $this

     */
    public function setInstallationNote($data);

    /**
     * @param $data
     * @return $this
     */
    public function setInstallationFee($data);

    /**
     * @return boolean
     */
    public function getAllowInstallation();

    /**
     * @param boolean $value
     * @return $this
     */
    public function setAllowInstallation($value);

    /**
     * @return string
     */
    public function getTooltipMessage();

    /**
     * @param string $message
     * @return $this
     */
    public function setTooltipMessage($message);
}

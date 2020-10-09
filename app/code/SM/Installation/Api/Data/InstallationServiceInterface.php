<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Installation\Api\Data;

/**
 * Interface InstallationServiceInterface
 * @package SM\Installation\Api\Data
 */
interface InstallationServiceInterface
{
    const ALLOW_INSTALLATION= 'allow_installation';
    const IS_INSTALLATION   = 'is_installation';
    const INSTALLATION_NOTE = 'installation_note';
    const INSTALLATION_FEE  = 'installation_fee';

    /**
     * @return int
     */
    public function getIsInstallation();

    /**
     * @param int $value
     * @return $this
     */
    public function setIsInstallation($value);

    /**
     * @return string
     */
    public function getInstallationNote();

    /**
     * @param string $value
     * @return $this
     */
    public function setInstallationNote($value);

    /**
     * @return float
     */
    public function getInstallationFee();

    /**
     * @param float $value
     * @return $this
     */
    public function setInstallationFee($value);

    /**
     * @return boolean
     */
    public function getAllowInstallation();

    /**
     * @param boolean $value
     * @return $this
     */
    public function setAllowInstallation($value);
}

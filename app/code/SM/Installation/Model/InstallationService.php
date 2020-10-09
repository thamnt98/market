<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Installation\Model;

use Magento\Framework\Model\AbstractModel;
use SM\Installation\Api\Data\InstallationServiceInterface;

/**
 * Class InstallationService
 * @package SM\Installation\Model
 */
class InstallationService extends AbstractModel implements \SM\Installation\Api\Data\InstallationServiceInterface
{
    /**
     * @inheritDoc
     */
    public function getIsInstallation()
    {
        return $this->getData(self::IS_INSTALLATION);
    }

    /**
     * @inheritDoc
     */
    public function setIsInstallation($value)
    {
        return $this->setData(self::IS_INSTALLATION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getInstallationNote()
    {
        return $this->getData(self::INSTALLATION_NOTE);
    }

    /**
     * @inheritDoc
     */
    public function setInstallationNote($value)
    {
        return $this->setData(self::INSTALLATION_NOTE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getInstallationFee()
    {
        return $this->getData(self::INSTALLATION_FEE);
    }

    /**
     * @inheritDoc
     */
    public function setInstallationFee($value)
    {
        return $this->setData(self::INSTALLATION_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAllowInstallation(){
        return $this->getData(self::ALLOW_INSTALLATION);
    }

    /**
     * @inheritDoc
     */
    public function setAllowInstallation($value){
        return $this->setData(self::ALLOW_INSTALLATION,$value);
    }
}

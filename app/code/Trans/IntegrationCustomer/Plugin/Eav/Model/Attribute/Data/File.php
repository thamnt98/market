<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Plugin\Eav\Model\Attribute\Data;

use Magento\Eav\Model\Attribute\Data\File as EavFile;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Plugin Class File
 */
class File
{
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $fileSystem;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @param \Magento\Framework\Filesystem\Io\File $fileSystem
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        \Magento\Framework\Filesystem\Io\File $fileSystem,
        DirectoryList $directoryList
    ) {
        $this->fileSystem = $fileSystem;
        $this->directoryList = $directoryList;
    }

    /**
     * update param value
     *
     * @param Magento\Eav\Model\Attribute\Data\File $subject
     * @param string $value
     */
    public function aroundValidateValue(EavFile $subject, callable $proceed, string $value)
    {
    	if (filter_var($value, FILTER_VALIDATE_URL)) { 
		    $tmpDir = $this->getMediaDirTmpDir();
		    $filename = baseName($value);
	    	$filePath = $tmpDir . $filename;

			/** read file from URL and copy it to the new destination */
			$result = $this->fileSystem->read($value, $filePath);

			$value = '/' . $filename;
		}
    	
    	return $proceed($value);
    }

    /**
     * Media directory name for the temporary file storage
     * pub/media/tmp
     *
     * @return string
     */
    protected function getMediaDirTmpDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'customer' . DIRECTORY_SEPARATOR;
    }
}

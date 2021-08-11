<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'MGS_Lookbook',
    __DIR__
);
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'Model/Entity/Attribute/Backend/License/License.php')) {
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Model/Entity/Attribute/Backend/License/License.php');
}
<?php
/**
 * Class AbstractInquire
 * @package SM\DigitalProduct\Model\Api\Inquire
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Api;

use SM\DigitalProduct\Api\Processor\DigitalAPIProcessorInterface;

/**
 * For Reorder only
 * Class AbstractInquire
 * @package SM\DigitalProduct\Model\Api
 */
abstract class AbstractInquire implements DigitalAPIProcessorInterface
{
    /**
     * @var \SM\DigitalProduct\Api\Inquire\InquireInterface
     */
    protected $inquireClass;

    /**
     * @var string
     */
    protected $inquireMethod;

    /**
     * AbstractInquire constructor.
     * @param \SM\DigitalProduct\Api\DigitalProductRepositoryInterface $inquireClass
     * @param null $inquireMethod
     */
    public function __construct(
        \SM\DigitalProduct\Api\Inquire\InquireInterface $inquireClass,
        $inquireMethod = null
    ) {
        $this->inquireClass = $inquireClass;
        $this->inquireMethod = $inquireMethod;
    }
}

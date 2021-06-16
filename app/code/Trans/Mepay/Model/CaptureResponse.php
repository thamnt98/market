<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Model;

use Trans\Mepay\Model\CaptureResponse\Oms;

class CaptureResponse 
{
    /**
     * @var string
     */
    const CAPTURE_RESPONSE_TYPE_OMS = 'oms';

    /**
     * @var \Trans\Mepay\Model\CaptureResponse\Oms
     */
    protected $oms;

    /**
     * @var array
     */
    protected $format;

    /**
     * Constructor
     * @param \Trans\Mepay\Model\CaptureResponse\Oms $oms
     */
    public function __construct(
        Oms $oms
    ) {
        $this->format = [];
        $this->oms = $oms;
    }

    /**
     * Generate capture response
     * @param  string $type 
     * @return void
     */
    public function generateFormat(string $type = self::CAPTURE_RESPONSE_TYPE_OMS)
    {
        if ($type === self::CAPTURE_RESPONSE_TYPE_OMS)
            $this->format = $this->oms->generateFormat();
    }

    /**
     * Get capture format
     * @return array
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set capture data
     * @param string $type
     * @param string data
     * @return void
     */
    public function setFormat(string $data, string $type = self::CAPTURE_RESPONSE_TYPE_OMS)
    {
        $input = json_decode($data, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            if ($type == self::CAPTURE_RESPONSE_TYPE_OMS) {
                $this->generateFormat($type);
                $this->oms->composeFormat($this->format, $input);
            }
        }
    }
}
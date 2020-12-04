<?php

namespace SM\MobileApi\Api\Data\HomepageMessage;

/**
 * Interface GreetingMessageInterface
 * @package SM\MobileApi\Api\Data\HomepageMessage
 */
 interface GreetingMessageInterface
 {
     const MESSAGE = 'message';
     const TIME_RANGE = 'time_range';
     const REDIRECT_TYPE = 'redirect_type';
     const CONFIG = 'config';

     /**
      * @return string
      */
     public function getMessage();

     /**
      * @param string $message
      * @return $this
      */
     public function setMessage($message);

     /**
      * @return string
      */
     public function getTimeRange();

     /**
      * @param string $time
      * @return $this
      */
     public function setTimeRange($time);

     /**
      * @return string
      */
     public function getRedirectType();

     /**
      * @param string $type
      * @return $this
      */
     public function setRedirectType($type);

     /**
      * @return \SM\MobileApi\Api\Data\HomepageMessage\ConfigMessageInterface[]
      */
     public function getConfig();

     /**
      * @param \SM\MobileApi\Api\Data\HomepageMessage\ConfigMessageInterface[] $config
      * @return $this
      */
     public function setConfig($config);
 }

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
namespace Trans\Mepay\Model\CardSavedToken;

use Trans\Mepay\Api\Data\CardSavedTokenInterface;

class Messages
{
    /**
     * @var string
     */
    const SAVE_FAILED_MESSAGE = 'Saving card token is unsuccesfull';

    /**
     * @var string
     */
    const SAVE_FAILED_FLAG = '[SAVING_TOKEN_FAILED]';

    /**
     * @var string
     */
    const DELETE_FAILED_MESSAGE = 'Deleting card token is unsuccessful';

    /**
     * @var string
     */
    const DELETE_FAILED_FLAG = '[DELETE_TOKEN_FAILED]';

    /**
     * @var string
     */
    const LOGGER_FILENAME = 'Card_Saving_Token.log';

    /**
     * @var string
     */
    const LOGGER_PATH = '/var/log/';

    /**
     * @var string
     */
    const ORIGINAL_FLAG = '[ORIGINAL_MESSAGE]';

    /**
     * @var string
     */
    const CUSTOM_FLAG = '[CUSTOM_MESSAGE]';

    /**
     * @var \Zend\Log\Logger
     */
    protected $logger;

    /**
     * Constructor
     */
    public function __construct()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . self::LOGGER_PATH.self::LOGGER_FILENAME);
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);
    }

    /**
     * Set custom message when failed save
     *
     * @param string $messages
     * @param string $token
     * @return void
     * @throws \Exception
     */
    public function saveFailed(string $messages = '', string $token = '')
    {
        $this->logger->info(self::SAVE_FAILED_FLAG);
        $messages = ($messages)? $messages : self::SAVE_FAILED_MESSAGE;
        $this->logger->info(self::ORIGINAL_FLAG.$messages);
        $messages .= ($token)? CardSavedTokenInterface::TOKEN.'-'.$token : '';
        $this->logger->info(self::CUSTOM_FLAG.$messages);
        throw new \Exception($messages, 1);
    }

    /**
     * Set custom message when failed on delete
     *
     * @param string $messages
     * @param string $token
     * @return void
     * @throws \Exception
     */
    public function deleteFailed(string $messages = '', string $token = '')
    {
        $this->logger->info(self::DELETE_FAILED_FLAG);
        $messages = ($messages)? $messages : self::DELETE_FAILED_MESSAGE;
        $this->logger->info(self::ORIGINAL_FLAG.$messages);
        $messages .= ($token)? CardSavedTokenInterface::TOKEN.'-'.$token : '';
        $this->logger->info(self::CUSTOM_FLAG.$messages);
        throw new \Exception($messages, 1);
    }
}
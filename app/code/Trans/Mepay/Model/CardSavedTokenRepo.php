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

use Trans\Mepay\Api\CardSavedTokenRepoInterface;
use Trans\Mepay\Api\Data\CardSavedTokenInterfaceFactory;
use Trans\Mepay\Model\CardSavedToken\Builder;
use Trans\Mepay\Model\CardSavedToken\Messages;

class CardSavedTokenRepo implements CardSavedTokenRepoInterface
{
    /**
     * @var \Trans\Mepay\Api\Data\CardSavedTokenInterfaceFactory
     */
    protected $modelFactory;

    /**
     * @var \Trans\Mepay\Model\CardSavedToken\Builder
     */
    protected $builder;

    /**
     * @var \Trans\Mepay\Model\CardSavedToken\Messages
     */
    protected $messages;

    /**
     * Constructor
     *
     * @param CardSavedTokenInterfaceFactory $modelFactory
     * @param Builder $builder
     * @param Messages $messages
     */
    public function __construct(
        CardSavedTokenInterfaceFactory $modelFactory,
        Builder $builder,
        Messages $messages
    ){
        $this->modelFactory = $modelFactory;
        $this->builder = $builder;
        $this->messages = $messages;
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->builder->getAll();
    }

    /**
     * @inheritdoc
     */
    public function saveAll($list)
    {
        try {
            foreach($list as $value) {
                $this->save($value);
            }
        } catch (\Exception $e) {
            $this->messages->saveFailed($e->getMessage());
        }
        return $list;
    }

    /**
     * @inheritdoc
     */
    public function deleteAll($list)
    {
        try {
            foreach ($list as $value) {
                $this->delete($value);
            }
        } catch (\Exception $e) {
            $this->messages->deleteFailed($e->getMessage());
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getBymethod($method)
    {
        return $this->builder->getByMethod($method);
    }

    /**
     * @inheritdoc
     */
    public function save($obj)
    {
        try {
            $this->builder->save($obj);
        } catch (\Exception $e) {
            $this->messages->saveFailed($e->getMessage());
        }
        return $$obj;
    }

    /**
     * @inheritdoc
     */
    public function delete($obj)
    {
        try {
            $this->builder->delete($obj);
        } catch (\Exception $e) {
            $this->messages->deleteFailed($e->getMessage());
        }
        return true;
    }

    public function setCustomerToken($obj)
    {
        try {
            $this->builder->setCustomerToken($obj);
        } catch (\Exception $e) {
            $this->messages->saveFailed($e->getMessage());
        }
        return $$obj;
    }
}
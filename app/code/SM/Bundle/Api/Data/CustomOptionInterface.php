<?php

namespace SM\Bundle\Api\Data;

interface CustomOptionInterface{
    const OPTION_ID = "option_id";
    const OPTION_VALUE = "option_value";

    /**
     * @return int
     */
    public function getOptionId();

    /**
     * @param int $data
     * @return $this
     */
    public function setOptionId($data);

    /**
     * @return int
     */
    public function getOptionValue();

    /**
     * @param int $data
     * @return $this
     */
    public function setOptionValue($data);
}
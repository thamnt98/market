<?php
namespace SM\InspireMe\Api\Data;

interface PostTopicInterface
{
    const TOPIC_ID = 'id';
    const TOPIC_NAME = 'name';

    /**
     * @return int
     */
    public function getTopicId();

    /**
     * @param int $data
     * @return $this
     */
    public function setTopicId($data);

    /**
     * @return string
     */
    public function getTopicName();

    /**
     * @param string $data
     * @return $this
     */
    public function setTopicName($data);


}

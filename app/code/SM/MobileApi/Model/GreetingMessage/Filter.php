<?php

namespace SM\MobileApi\Model\GreetingMessage;

use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Filter
 * @package SM\MobileApi\Model\GreetingMessage
 */
class Filter
{
    /**
     * @var TimezoneInterface
     */
    public $timezone;

    /**
     * @var DateTimeFactory
     */
    public $dateTime;

    /**
     * Filter constructor.
     * @param TimezoneInterface $timezone
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        TimezoneInterface $timezone,
        DateTimeFactory $dateTimeFactory
    ) {
        $this->timezone = $timezone;
        $this->dateTime = $dateTimeFactory;
    }

    /**
     * @param array $greetingMessage
     * @return array
     */
    public function filterMessage(array $greetingMessage)
    {
        $filterMessage = [];
        foreach ($greetingMessage as $value) {
            $validate = $this->validate($value[Listing::START_TIME], $value[Listing::END_TIME]);
            if ($validate) {
                $filterMessage = $value;
                break;
            }
        }

        return $filterMessage;
    }

    /**
     * Validate time in range
     * @param string $startTime
     * @param string $endTime
     * @return bool
     */
    protected function validate(string $startTime, string $endTime)
    {
        if ($startTime == $endTime) {
            return false;
        }

        $datetime = $this->dateTime->create();

        $startTime   = $datetime->date('h:i:s A', $startTime);
        $endTime     = $datetime->date('h:i:s A', $endTime);
        $currentTime = $this->timezone->date()->format('h:i:s A');

        if (strtotime($currentTime) >= strtotime($startTime) && strtotime($currentTime) <= strtotime($endTime)) {
            return true;
        } else {
            return false;
        }
    }
}

<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: December, 07 2020
 * Time: 5:59 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Observer;

class HelpQuestionSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SM\Notification\Model\Notification\Generate
     */
    protected $generate;

    /**
     * @var \SM\Notification\Helper\Config
     */
    protected $config;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Logger\Monolog|null
     */
    protected $logger;

    /**
     * HelpTopicSaveAfter constructor.
     *
     * @param \SM\Notification\Helper\Config                    $config
     * @param \SM\Notification\Model\Notification\Generate      $generate
     * @param \SM\Notification\Model\ResourceModel\Notification $resource
     * @param \Magento\Framework\Logger\Monolog|null            $logger
     */
    public function __construct(
        \SM\Notification\Helper\Config $config,
        \SM\Notification\Model\Notification\Generate $generate,
        \SM\Notification\Model\ResourceModel\Notification $resource,
        \Magento\Framework\Logger\Monolog $logger = null
    ) {
        $this->generate = $generate;
        $this->config = $config;
        $this->resource = $resource;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \SM\Help\Model\Question $question */
        $question = $observer->getEvent()->getData('entity');

        if (!$question || !$question->getId() || !$question->hasDataChanges()) {
            return;
        }

        $topic = $question->getTopicId();
        $termId = $this->config->getTermHelpId($question->getStoreId());
        $policeId = $this->config->getPolicyHelpId($question->getStoreId());

        if ($topic
            && ($topic == $termId || $topic == $policeId)
            && (
                $question->getTitle() !== $question->getOrigData('title')
                || $question->getTopicId() != $question->getOrigData('topic_id')
                || $question->getContent() !== $question->getOrigData('question')
                || ($question->getStatus() == 1 && $question->getOrigData('status') != 1)
            )
        ) {
            try {
                $this->resource->save($this->generate->termAndPolicy($topic, $question->getStoreId()));
            } catch (\Exception $e) {
                if ($this->logger) {
                    $this->logger->error("Term and Condition, Privacy Police: \n" . $e->getMessage(), $e->getTrace());
                }
            }
        }
    }
}

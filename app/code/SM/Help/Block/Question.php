<?php
namespace SM\Help\Block;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use SM\Help\Api\Data\QuestionInterface;

/**
 * Class CMSPageMobile
 * @package SM\MobileApi\Block
 */
class Question extends Template
{
    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    protected $question;

    protected $resource;

    protected $request;

    /**
     * CMSPageMobile constructor.
     * @param FilterProvider $filterProvider
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        FilterProvider $filterProvider,
        Template\Context $context,
        \SM\Help\Model\QuestionFactory $question,
        \SM\Help\Model\ResourceModel\Question $resource,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->resource = $resource;
        $this->question = $question;
        $this->filterProvider = $filterProvider;
        $this->request = $request;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getContentById()
    {
        $questionId = $this->request->getParam('id');
        $question = $this->question->create();
        $this->resource->load($question, $questionId);
        if (!$question->getId() && !$question->getContent()) {
            return "";
        }
        return $question->getContent();
    }

    /**
     * @param $content
     * @return string
     * @throws \Exception
     */
    public function getContentWYSIWYG($content)
    {
        return $this->filterProvider->getBlockFilter()->filter($content);
    }
}

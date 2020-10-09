<?php

namespace SM\ViewLog\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'SM_ViewLog::viewlog';

    protected $resultPageFactory = false;
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->dir = $dir;
        $this->request = $request;
        $this->urlInterface = $urlInterface;
    }

    public function execute()
    {
        $logDir = $this->dir->getPath('var');

        $dir = str_replace('___', '/', $this->request->getParam('dir'));
        if (empty($dir)) {
            $dir = 'log';
        }
        $logDir = $logDir . '/' . $dir;

        $files = scandir($logDir, 1);

        $file = str_replace('___', '/', $this->request->getParam('file'));
        $file = $logDir . '/' . $file;

        $fn = fopen($file,"r");

        echo "<html>";
        echo "<body>";


        echo "<pre>";
        while(! feof($fn))  {
            $result = fgets($fn);
            echo $result;
        }

        ///////////////////////////////////
        echo ("</br> =====================================================================================================");
        echo ("</br> =====================================================================================================");
        echo ("</br> =====================================================================================================</br>");
        echo ("</br>");
//        echo "<pre>";
//        print_r($files);

        foreach($files as $_file) {
            //echo 'dir/' . $dir . '/file/' . $_file . '<br>';
            $href = $this->getUrl('*/*/*', ['_current' => false, '_use_rewrite' => true]) . 'dir/' . $dir . '/file/' . $_file;
            echo "<a href='{$href}'>{$href}</a>" . "<br>";
        }


        fclose($fn);



        echo "</body>";
        echo "</html>";

    }
}

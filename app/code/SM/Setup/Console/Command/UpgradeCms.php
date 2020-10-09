<?php
/**
 * Class FileDataUrl
 * @package SM\Theme\Model\Config\ContentType\AdditionalData\Provider
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Setup\Console\Command;

use Magento\Cms\Api\Data\PageInterfaceFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpgradeCms extends Command
{
    const PAGE = 'page';
    const BLOCK = 'block';
    const TYPE = 'type';
    const IDENTIFIER = 'identifier';
    const ALL = 'all';
    const STATIC_CONTENT_PATH = 'static-content';
    const PAGE_CONTENT_PATH = self::STATIC_CONTENT_PATH . '/pages';
    const BLOCK_CONTENT_PATH = self::STATIC_CONTENT_PATH . '/blocks';
    const ALLOW_FILES_PATTERN = '#.html#';

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var \Magento\Cms\Api\BlockRepositoryInterfaceAGE
     */
    private $blockRepository;

    /**
     * @var \Magento\Cms\Api\GetPageByIdentifierInterface
     */
    private $getPageByIdentifier;

    /**
     * @var \Magento\Cms\Api\GetBlockByIdentifierInterface
     */
    private $getBlockByIdentifier;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $driverFile;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @var PageInterfaceFactory
     */
    private $pageInterfaceFactory;

    /**
     * @var \Magento\Cms\Api\Data\BlockInterfaceFactory
     */
    private $blockInterfaceFactory;

    /**
     * UpgradeCms constructor.
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepository
     * @param PageInterfaceFactory $pageInterfaceFactory
     * @param \Magento\Cms\Api\Data\BlockInterfaceFactory $blockInterfaceFactory
     * @param \Magento\Cms\Api\BlockRepositoryInterface $blockRepository
     * @param \Magento\Cms\Api\GetPageByIdentifierInterface $getPageByIdentifier
     * @param \Magento\Cms\Api\GetBlockByIdentifierInterface $getBlockByIdentifier
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Magento\Cms\Api\Data\PageInterfaceFactory $pageInterfaceFactory,
        \Magento\Cms\Api\Data\BlockInterfaceFactory $blockInterfaceFactory,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \Magento\Cms\Api\GetPageByIdentifierInterface $getPageByIdentifier,
        \Magento\Cms\Api\GetBlockByIdentifierInterface $getBlockByIdentifier,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->pageRepository = $pageRepository;
        $this->pageInterfaceFactory = $pageInterfaceFactory;
        $this->blockInterfaceFactory = $blockInterfaceFactory;
        $this->blockRepository = $blockRepository;
        $this->getPageByIdentifier = $getPageByIdentifier;
        $this->getBlockByIdentifier = $getBlockByIdentifier;
        $this->driverFile = $driverFile;
        $this->directoryList = $directoryList;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::TYPE,
                't',
                InputOption::VALUE_REQUIRED,
                'Page or Block'
            ),
            new InputOption(
                self::IDENTIFIER,
                'i',
                InputOption::VALUE_REQUIRED,
                'Update List Identifier'
            ),
        ];

        $this->setName('sm:setup:upgrade-content')
            ->setDefinition($options)
            ->setDescription('Upgrade CMS Static content into Database');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $type = $input->getOption(self::TYPE);

            if (empty($type)) {
                $output->writeln('Type Argument Require Fields.');
                return \Magento\Framework\Console\Cli::RETURN_FAILURE;
            }

            switch ($type) {
                case self::PAGE:
                    $identifier = $input->getOption(self::IDENTIFIER);
                    if (empty($identifier)) {
                        $output->writeln('Identifier Argument Require Fields.');
                        return \Magento\Framework\Console\Cli::RETURN_FAILURE;
                    }
                    $this->updateCmsPage($identifier);
                    break;
                case self::BLOCK:
                    $identifier = $input->getOption(self::IDENTIFIER);
                    if (empty($identifier)) {
                        $output->writeln('Identifier Argument Require Fields.');
                        return \Magento\Framework\Console\Cli::RETURN_FAILURE;
                    }
                    $this->updateCmsBlock($identifier);
                    break;
                case self::ALL:
                    $this->updateAllCmsContent();
                    break;
                default:
                    break;
            }

            $output->writeln('Done.');
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }

    /**
     * @param $identifier
     * @throws FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateCmsPage($identifier)
    {
        $pagesToUpdate = $this->getAllPagesToUpdate();
        $identifierArray = $this->getIdentifierArray($identifier);

        foreach ($pagesToUpdate as $key => $pageToUpdate) {
            if ($key == 0) {
                continue;
            }

            $pageToUpdate = explode(',', $pageToUpdate);
            $count = count($pageToUpdate);

            if ($count < 2) {
                continue;
            }

            if (!isset($pageToUpdate[2])) {
                $pageToUpdate[2] = '1column';
            }

            if (!isset($pageToUpdate[3])) {
                $pageToUpdate[3] = 0;
            }

            if ($identifier !== self::ALL) {
                foreach ($identifierArray as $identifier) {
                    if ($pageToUpdate[0] == $identifier) {
                        $page = $this->getPageByIdentifier($identifier, $pageToUpdate[3]);
                        break;
                    }
                }
            } else {
                $page = $this->getPageByIdentifier($pageToUpdate[0], $pageToUpdate[3]);
            }

            if (isset($page) && $page instanceof \Magento\Cms\Api\Data\PageInterface) {
                $page->setTitle($pageToUpdate[1]);
                $page->setPageLayout(trim($pageToUpdate[2]));

                $file = $this->getPageFilePath($pageToUpdate[0] . '.html');
                if ($this->driverFile->isExists($file)) {
                    $content = $this->getFileContent($file);
                    $page->setContent($content);
                }

                if (empty($page->getId())) {
                    $page->setIdentifier($pageToUpdate[0]);
                    $page->setStoreId(trim($pageToUpdate[3]));
                }
                $this->pageRepository->save($page);
            }
        }
    }

    /**
     * @param $identifier
     * @throws FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateCmsBlock($identifier)
    {
        $blocksToUpdate = $this->getAllBlocksToUpdate();
        $identifierArray = $this->getIdentifierArray($identifier);

        foreach ($blocksToUpdate as $key => $blockToUpdate) {
            if ($key == 0) {
                continue;
            }

            $blockToUpdate = explode(',', $blockToUpdate);
            $count = count($blockToUpdate);

            if ($count < 2) {
                continue;
            }

            if (!isset($blockToUpdate[2])) {
                $blockToUpdate[2] = 0;
            }

            if ($identifier !== self::ALL) {
                foreach ($identifierArray as $identifier) {
                    if ($blockToUpdate[0] == $identifier) {
                        $block = $this->getBlockByIdentifier($identifier, $blockToUpdate[2]);
                        break;
                    }
                }
            } else {
                $block = $this->getBlockByIdentifier($blockToUpdate[0], $blockToUpdate[2]);
            }

            if (isset($block) && $block instanceof \Magento\Cms\Api\Data\BlockInterface) {
                $block->setTitle($blockToUpdate[1]);
                $file = $this->getBlockFilePath($blockToUpdate[0] . '.html');

                if ($this->driverFile->isExists($file)) {
                    $content = $this->getFileContent($file);
                    $block->setContent($content);
                }

                if (empty($block->getId())) {
                    $block->setIdentifier($blockToUpdate[0]);
                    $block->setStoreId([trim($blockToUpdate[2])]);
                }
                $this->blockRepository->save($block);
            }
        }
    }

    /**
     * @throws FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateAllCmsContent()
    {
        $this->updateCmsPage(self::ALL);
        $this->updateCmsBlock(self::ALL);
    }

    /**
     * @param $identifier
     * @return array
     */
    private function getIdentifierArray($identifier)
    {
        if (is_string($identifier) && !is_array($identifier)) {
            return explode(',', $identifier);
        }

        return $identifier;
    }

    /**
     * @return array
     * @throws FileSystemException
     */
    private function getAllPagesToUpdate()
    {
        $file = $this->getPageFilePath('list.html');
        $fileContent = $this->driverFile->fileGetContents($file);
        return explode(PHP_EOL, $fileContent);
    }

    /**
     * @return array
     * @throws FileSystemException
     */
    private function getAllBlocksToUpdate()
    {
        $file = $this->getBlockFilePath('list.html');
        $fileContent = $this->driverFile->fileGetContents($file);
        return explode(PHP_EOL, $fileContent);
    }

    /**
     * @param $path
     * @return array|string[]
     */
    private function getAllFilesInDirectory($path)
    {
        try {
            $directoryPath = $this->getRootDirectory() . '/' . $path;

            if ($this->driverFile->isExists($directoryPath)) {
                return $this->driverFile->readDirectory($directoryPath);
            }
        } catch (FileSystemException $e) {
            var_dump($e->getMessage());
        }

        return [];
    }

    /**
     * @param $identifier
     * @param $storeId
     * @return \Magento\Cms\Api\Data\PageInterface
     */
    private function getPageByIdentifier($identifier, $storeId)
    {
        try {
            $page = $this->getPageByIdentifier->execute($identifier, $storeId);
        } catch (NoSuchEntityException $e) {
            $page = $this->pageInterfaceFactory->create();
        }

        return $page;
    }

    /**
     * @param $identifier
     * @param $storeId
     * @return \Magento\Cms\Api\Data\BlockInterface
     */
    private function getBlockByIdentifier($identifier, $storeId)
    {
        try {
            $block = $this->getBlockByIdentifier->execute($identifier, $storeId);
        } catch (NoSuchEntityException $e) {
            $block = $this->blockInterfaceFactory->create();
        }

        return $block;
    }

    /**
     * @return string
     */
    private function getRootDirectory()
    {
        return $this->directoryList->getRoot();
    }

    /**
     * @param $file
     * @return string
     */
    private function getPageFilePath($file)
    {
        return $this->getRootDirectory() . '/' . self::PAGE_CONTENT_PATH . '/' . $file;
    }

    /**
     * @param $file
     * @return string
     */
    private function getBlockFilePath($file)
    {
        return $this->getRootDirectory() . '/' . self::BLOCK_CONTENT_PATH . '/' . $file;
    }

    /**
     * @param $file
     * @return string
     * @throws FileSystemException
     */
    private function getFileContent($file)
    {
        if (!preg_match(self::ALLOW_FILES_PATTERN, $file)) {
            return '';
        }

        return $this->driverFile->fileGetContents($file);
    }
}

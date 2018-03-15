<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-sphinx
 * @version   1.0.49
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Store\Model\StoreManager;
use Symfony\Component\Console\Command\Command;
use Mirasvit\Search\Model\SynonymFactory;
use Mirasvit\Search\Model\Config;

class SynonymCommand extends Command
{
    /**
     * @var SynonymFactory
     */
    protected $synonymFactory;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param SynonymFactory $synonymFactory
     * @param StoreManager   $storeManager
     * @param Config         $config
     */
    public function __construct(
        SynonymFactory $synonymFactory,
        StoreManager $storeManager,
        Config $config
    ) {
        $this->synonymFactory = $synonymFactory;
        $this->storeManager = $storeManager;
        $this->config = $config;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                'file',
                null,
                InputOption::VALUE_REQUIRED,
                'Synonyms file'
            ),
            new InputOption(
                'store',
                null,
                InputOption::VALUE_REQUIRED,
                'Store Id'
            ),
            new InputOption(
                'remove',
                null,
                InputOption::VALUE_NONE,
                'remove'
            )
        ];

        $this->setName('mirasvit:search:synonym')
            ->setDescription('Import synonyms')
            ->setDefinition($options);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('remove')) {
            $store = $input->getOption('store');

            $collection = $this->synonymFactory->create()->getCollection();
            if ($store) {
                $collection->addFieldToFilter('store_id', $store);
            }

            $cnt = 0;
            foreach ($collection as $item) {
                $item->delete();
                $cnt++;

                if ($cnt % 1000 == 0) {
                    $output->writeln("<info>$cnt synonyms are removed...</info>");
                }
            }

            $output->writeln("<info>$cnt synonyms are removed.</info>");

            return;
        }

        if ($input->getOption('file') && $input->getOption('store')) {
            $file = $this->config->getSynonymDirectoryPath() . DIRECTORY_SEPARATOR . $input->getOption('file');
            $store = $input->getOption('store');

            $result = $this->synonymFactory->create()
                ->import($file, $store);

            $output->writeln("<info>Imported {$result['synonyms']} synonyms</info>");
        } else {
            $output->writeln('<info>Available files:</info>');
            foreach ($this->synonymFactory->create()->getAvailableFiles() as $file) {
                $info = pathinfo($file);
                $output->writeln("    {$info['basename']}");
            }

            $output->writeln('<info>Available stores:</info>');
            foreach ($this->storeManager->getStores(true) as $store) {
                $output->writeln("    {$store->getId()} [{$store->getCode()}]");
            }
        }

    }
}

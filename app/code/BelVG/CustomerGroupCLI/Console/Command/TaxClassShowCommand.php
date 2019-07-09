<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 ********************************************************************
 * @category   BelVG
 * @package    BelVG_CustomerGroupCLI
 * @copyright  Copyright (c) BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

namespace BelVG\CustomerGroupCLI\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * Class TaxClassShowCommand
 *
 * @package BelVG\CustomerGroupCLI\Console\Command
 */
class TaxClassShowCommand extends Command
{
    /** @var \Magento\Tax\Model\TaxClass\Source\Customer */
    protected $taxClass;

    /**
     * TaxClassShowCommand constructor.
     *
     * @param \Magento\Tax\Model\TaxClass\Source\Customer $taxClass
     */
    public function __construct(
        \Magento\Tax\Model\TaxClass\Source\Customer $taxClass
    ) {
        $this->taxClass = $taxClass;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('tax:class:show')
            ->setDescription('Show tax classes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $taxClasses = $this->taxClass->getAllOptions();
            $tableRows = [];
            foreach ($taxClasses as $taxClass) {
                $tableRows[] = [$taxClass['value'], $taxClass['label']];
            }

            $table = new Table($output);
            $table
                ->setHeaders(['Id', 'Name'])
                ->setRows($tableRows);
            $table->render();

            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}

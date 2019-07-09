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
use Symfony\Component\Console\Input\InputOption;

/**
 * Class TaxClassCreateCommand
 *
 * @package BelVG\CustomerGroupCLI\Console\Command
 */
class TaxClassCreateCommand extends Command
{
    const NAME = 'tax_class_name';

    protected $taxClassModelFactory;

    public function __construct(
        \Magento\Tax\Model\ClassModelFactory $taxClassModelFactory
    ) {
        $this->taxClassModelFactory = $taxClassModelFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $options = [
            new InputOption(
                self::NAME,
                null,
                InputOption::VALUE_REQUIRED,
                '(Required) Tax class name'
            )
        ];

        $this
            ->setName('tax:class:create')
            ->setDescription('Create new tax class')
            ->setDefinition($options)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $taxClassName = $input->getOption(self::NAME);

            $taxClassModel = $this->taxClassModelFactory->create();
            $newTaxClassId = $taxClassModel
                ->setClassName($taxClassName)
                ->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER)
                ->save()
                ->getId();

            $message = '<info>New tax customer class '. $taxClassName . ' has been created with ID = ' . $newTaxClassId . '</info>';
            $output->writeln($message);

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

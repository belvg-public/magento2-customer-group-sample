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
 * Class CustomerGroupDelete
 *
 * @package BelVG\CustomerGroupCLI\Console\Command
 */
class CustomerGroupDeleteCommand extends Command
{
    const GROUP_ID = 'group-id';

    /** @var \Magento\Customer\Api\GroupRepositoryInterface  */
    protected $groupRepository;


    public function __construct(
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
    ) {
        $this->groupRepository = $groupRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('customer:group:delete')
            ->setDescription('Customer group deleting')
            ->setDefinition($this->getOptionsList())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $customerGroupId = $input->getOption(self::GROUP_ID);
            $customerGroup = $this->groupRepository->getById($customerGroupId);
            $this->groupRepository->delete($customerGroup);

            $message = '<info>Customer group ' . $customerGroup->getCode() . ' has been deleted</info>';
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

    /**
     * @return array
     */
    protected function getOptionsList()
    {
        return [
            new InputOption(self::GROUP_ID, null, InputOption::VALUE_REQUIRED, '(Required) Customer Group Id'),
        ];
    }
}

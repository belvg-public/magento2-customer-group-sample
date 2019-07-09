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
class CustomerAssignGroupCommand extends Command
{
    const USER_ID = 'user-id';
    const GROUP_ID = 'group-id';

    /** @var \Magento\Customer\Api\GroupRepositoryInterface  */
    protected $groupRepository;

    /** @var \Magento\Customer\Api\CustomerRepositoryInterface  */
    protected $customerRepository;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('customer:assign:group')
            ->setDescription('Assign group for customer')
            ->setDefinition($this->getOptionsList())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $customerId = $input->getOption(self::USER_ID);
            $customerGroupId = $input->getOption(self::GROUP_ID);
            $customerGroup = $this->groupRepository->getById($customerGroupId);

            $customer = $this->customerRepository->getById($customerId);
            $customer->setGroupId($customerGroupId);
            $this->customerRepository->save($customer);

            $message = '<info>Customer Group for customer ' . $customer->getFirstname() . ' ' . $customer->getLastname()
                . ' has been changed to ' . $customerGroup->getCode() . '</info>';
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
            new InputOption(self::USER_ID, null, InputOption::VALUE_REQUIRED, '(Required) Customer Id '),
            new InputOption(self::GROUP_ID, null, InputOption::VALUE_REQUIRED, '(Required) Customer Group Id'),
        ];
    }
}

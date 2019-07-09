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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CustomerGroupCreateCommand
 *
 * @package BelVG\CustomerGroupCLI\Console\Command
 */
class CustomerGroupCreateCommand extends Command
{
    /** @var \BelVG\CustomerGroupCLI\Helper\CustomerGroup */
    protected $customerGroupHelper;

    /** @var \Magento\User\Model\UserFactory */
    protected $userFactory;

    /** @var \Magento\Authorization\Model\RoleFactory */
    private $roleFactory;

    /** @var \Magento\Authorization\Model\RulesFactory */
    private $rulesFactory;

    public function __construct(
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Authorization\Model\RulesFactory $rulesFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \BelVG\CustomerGroupCLI\Helper\CustomerGroup $customerGroupHelper
    ) {
        $this->roleFactory = $roleFactory;
        $this->rulesFactory = $rulesFactory;
        $this->userFactory = $userFactory;
        $this->customerGroupHelper = $customerGroupHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('customer:group:create')
            ->setDescription('Create new customer group')
            ->setDefinition($this->getOptionsList());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln('<info>Creating new Customer group...</info>');
            $this->customerGroupHelper->setData($input);
            $customerGroupId = $this->customerGroupHelper->execute();
            $output->writeln('');
            $output->writeln('<info>Customer Group has been created</info>');
            $output->writeln('<comment>Customer Group Code: '
                . $input->getOption(\BelVG\CustomerGroupCLI\Helper\CustomerGroup::KEY_CODE));
            $output->writeln('<comment>Customer Group tax class Id: '
                . $input->getOption(\BelVG\CustomerGroupCLI\Helper\CustomerGroup::KEY_TAX_CLASS_ID));
            $output->writeln('<comment>Customer Group Id: ' . $customerGroupId);
        } catch (\Exception $e) {
            $output->writeln('');
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

    /**
     * @return array
     */
    protected function getOptionsList()
    {
        return [
            new InputOption(\BelVG\CustomerGroupCLI\Helper\CustomerGroup::KEY_CODE, null, InputOption::VALUE_REQUIRED,
                '(Required) Code for new customer group. '),
            new InputOption(\BelVG\CustomerGroupCLI\Helper\CustomerGroup::KEY_TAX_CLASS_ID, null,
                InputOption::VALUE_REQUIRED,
                '(Required) Tax customer class ID. Use commands tax:class:show and tax:class:create'),
        ];
    }
}
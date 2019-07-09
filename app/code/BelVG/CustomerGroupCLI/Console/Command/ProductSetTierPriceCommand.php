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
 * Class ProductSetTierPriceCommand
 *
 * @package BelVG\CustomerGroupCLI\Console\Command
 */
class ProductSetTierPriceCommand extends Command
{
    const PRICE_VALUE = 'price-value';
    const PRODUCT_QTY = 'product-qty';
    const PRODUCT_SKU = 'product-sku';
    const CUSTOMER_GROUP_ID = 'customer-group-id';

    /** @var \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory  */
    protected $productTierPriceFactory;

    /** @var \Magento\Catalog\Api\ScopedProductTierPriceManagementInterface  */
    protected $productTierPriceManagement;

    /** @var \Magento\Framework\App\State  */
    protected $state;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $productTierPriceFactory,
        \Magento\Catalog\Api\ScopedProductTierPriceManagementInterface $productTierPriceManagement
    ) {
        $this->state = $state;
        $this->productTierPriceManagement = $productTierPriceManagement;
        $this->productTierPriceFactory = $productTierPriceFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('product:set:tier_price')
            ->setDescription('Set tier price')
            ->setDefinition($this->getOptionsList())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->emulateAreaCode('adminhtml', function () use ($input, $output){
                $productSku = (string)$input->getOption(self::PRODUCT_SKU);
                $priceValue = (float)$input->getOption(self::PRICE_VALUE);
                $productQty = (float)$input->getOption(self::PRODUCT_QTY);
                $customerGroupId = (int)$input->getOption(self::CUSTOMER_GROUP_ID);

                /** @var \Magento\Catalog\Api\Data\ProductTierPriceInterface $productTierPrice */
                $productTierPrice = $this->productTierPriceFactory->create();

                $productTierPrice->setCustomerGroupId($customerGroupId);
                $productTierPrice->setValue($priceValue);
                $productTierPrice->setQty($productQty);
                $this->productTierPriceManagement->add($productSku, $productTierPrice);

                $message = '<info>Tier price '. number_format($priceValue, 2) .' for product with SKU =  ' . $productSku . ' has been  added.</info>';
                $output->writeln($message);

                return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
            });
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $output->write($e->getTraceAsString());
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
            new InputOption(self::PRODUCT_SKU, null, InputOption::VALUE_REQUIRED, '(Required) Product SKU'),
            new InputOption(self::PRICE_VALUE, null, InputOption::VALUE_REQUIRED, '(Required)  Price Value '),
            new InputOption(self::PRODUCT_QTY, null, InputOption::VALUE_REQUIRED, '(Required) Product Quantity'),
            new InputOption(self::CUSTOMER_GROUP_ID, null, InputOption::VALUE_REQUIRED, '(Required) Customer Group Id'),
        ];
    }
}

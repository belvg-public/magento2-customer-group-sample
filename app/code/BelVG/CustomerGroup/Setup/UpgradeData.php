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
 * @package    BelVG_CustomerGroup
 * @copyright  Copyright (c) BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

namespace BelVG\CustomerGroup\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 *
 * @package BelVG\CustomerGroup\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface  */
    protected $customerRepository;

    /** @var \Magento\Customer\Model\Group  */
    protected $groupFactory;

    /** @var GroupRepositoryInterface|\Magento\Customer\Api\GroupRepositoryInterface  */
    protected $groupRepository;

    /** @var \Magento\Catalog\Api\ScopedProductTierPriceManagementInterface  */
    protected $productTierPriceManagement;

    /** @var \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory  */
    protected $productTierPriceFactory;

    /** @var \Magento\Framework\App\State  */
    protected $state;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Framework\App\State                                   $state
     * @param \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory     $productTierPriceFactory
     * @param \Magento\Catalog\Api\ScopedProductTierPriceManagementInterface $productTierPriceManagement
     * @param \Magento\Customer\Api\GroupRepositoryInterface                 $groupRepository
     * @param \Magento\Customer\Model\GroupFactory                           $groupFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface              $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $productTierPriceFactory,
        \Magento\Catalog\Api\ScopedProductTierPriceManagementInterface $productTierPriceManagement,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->state = $state;
        $this->productTierPriceFactory = $productTierPriceFactory;
        $this->productTierPriceManagement = $productTierPriceManagement;
        $this->groupRepository = $groupRepository;
        $this->groupFactory = $groupFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $group = $this->groupFactory->create();
            $group->load('BelVG Group (Bel VG tax class)', 'customer_group_code');
            $customer = $this->customerRepository->getById(1);
            $customer->setGroupId($group->getId());
            $this->customerRepository->save($customer);
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            //Load BelVG Group (Retail Customer tax group
            $removedGroup = $this->groupRepository->getById(4);
            $this->groupRepository->delete($removedGroup);
        }

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            try {
                $this->state->emulateAreaCode('adminhtml', function () {
                    $productSku = '24-MB01';
                    /** @var \Magento\Catalog\Api\Data\ProductTierPriceInterface $productTierPrice */
                    $productTierPrice = $this->productTierPriceFactory->create();
                    $tierPriceValue = 9.00;
                    $productTierPrice->setCustomerGroupId(32000);
                    $productTierPrice->setValue($tierPriceValue);
                    $productTierPrice->setQty(3.00);
                    $this->productTierPriceManagement->add($productSku, $productTierPrice);
                });

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}

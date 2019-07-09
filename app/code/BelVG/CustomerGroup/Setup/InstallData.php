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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 *
 * @package BelVG\CustomerGroup\Setup
 */
class InstallData implements InstallDataInterface
{
    /** @var \Magento\Customer\Model\GroupFactory */
    protected $groupFactory;

    /** @var \Magento\Tax\Model\TaxClass\Source\Customer */
    protected $taxClass;

    /** @var \Magento\Tax\Model\ClassModelFactory */
    protected $classModelFactory;

    public function __construct(
        \Magento\Tax\Model\ClassModelFactory $classModelFactory,
        \Magento\Tax\Model\TaxClass\Source\Customer $taxClass,
        \Magento\Customer\Model\GroupFactory $groupFactory
    ) {
        $this->classModelFactory = $classModelFactory;
        $this->taxClass = $taxClass;
        $this->groupFactory = $groupFactory;
    }

    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        //You can use existing tax classes
        $existingTaxClassId = $this->taxClass->getOptionId('Retail Customer');

        //Or you can create a new customer tax Class
        $classModel = $this->classModelFactory->create();
        $newTaxClassId = $classModel
            ->setClassName('BelVG Tax Class')
            ->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->save()
            ->getId();

        //Creating new customer groups
        /** @var \Magento\Customer\Model\Group $group */
        $groupWithExistingTaxClass = $this->groupFactory->create();
        $groupWithExistingTaxClass
            ->setCode('BelVG Group (Retail Customer tax class)')
            ->setTaxClassId($existingTaxClassId)
            ->save();

        /** @var \Magento\Customer\Model\Group $group */
        $groupWithNewTaxClass = $this->groupFactory->create();
        $groupWithNewTaxClass
            ->setCode('BelVG Group (BelVG tax class)')
            ->setTaxClassId($newTaxClassId)
            ->save();

        $setup->endSetup();
    }
}

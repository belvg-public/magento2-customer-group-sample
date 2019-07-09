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

namespace BelVG\CustomerGroupCLI\Helper;

use \Magento\Framework\App\Helper\Context;

/**
 * Class CustomerGroup
 *
 * @package BelVG\CustomerGroupCLI\Helper
 */
class CustomerGroup extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**  */
    const KEY_CODE = 'code';

    /**  */
    const KEY_TAX_CLASS_ID = 'tax-class-id';

    /** @var  \Symfony\Component\Console\Input\InputInterface */
    protected $data;

    /** @var \Magento\Customer\Model\GroupFactory */
    protected $groupFactory;

    public function __construct(
        \Magento\Customer\Model\GroupFactory $groupFactory,
        Context $context
    ) {
        $this->groupFactory = $groupFactory;
        parent::__construct($context);
    }

    public function setData(\Symfony\Component\Console\Input\InputInterface $input)
    {
        $this->data = $input;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $customerGroup = $this->groupFactory->create();
        $customerGroupId = $customerGroup
            ->setCode($this->data->getOption(self::KEY_CODE))
            ->setTaxClassId($this->data->getOption(self::KEY_TAX_CLASS_ID))
            ->save()
            ->getId();

        return $customerGroupId;
    }
}
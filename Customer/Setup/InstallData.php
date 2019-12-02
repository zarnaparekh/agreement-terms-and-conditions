<?php

namespace Artera\Customer\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Install data
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    /**
     * CustomerSetupFactory
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * $attributeSetFactory
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * initiate object
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * install data method
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        /**
         * customer registration form default field mobile number
         */

        $attributesInfo = [
                'add_timestamp' => [
                    'type' => 'datetime',
                    'label' => 'TnC Accepted Date',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'input' => 'date',
                    'required' => false,
                    'visible' => false,
                    'searchable' => true,
                    'system' => 0,
                    'user_defined' => false,
                    'is_used_in_grid'  => true,
                    'is_visible_in_grid' => true
                ],
                'terms_enable' => [
                    'type' => 'int',
                    'label' => 'TnC Accepted',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'input' => 'boolean',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'searchable' => true,
                    'required' => false,
                    'system' => 0,
                    'visible' => false,
                    'user_defined' => false,
                    'is_used_in_grid'   => true,
                    'is_visible_in_grid' => true
                ]
            ];

        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerSetup->addAttribute(Customer::ENTITY, $attributeCode, $attributeParams);
            $attribute = $customerSetup->getEavConfig()
                ->getAttribute(Customer::ENTITY, $attributeCode)
                ->addData(
                    [
                        'attribute_set_id' => $attributeSetId,
                        'attribute_group_id' => $attributeGroupId,
                        'used_in_forms'=> ['adminhtml_customer', 'customer_account_create']
                    ]
                );
            $attribute->save();
        }
        $setup->endSetup();
    }
}

<?php

namespace Artera\Customer\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CustomerRegisterObserver implements ObserverInterface
{
    protected $_request;
    protected $_objectManager = null;
    protected $_customerGroup;
    protected $customerRepository;
    protected $customerFactory;
    protected $customer;
    protected $date;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->_objectManager = $objectManager;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customer = $customer;
        $this->date = $date;
    }

    public function execute(EventObserver $observer)
    {
        $event = $observer->getEvent();
        $customers = $observer->getCustomer();
        $customerId = $customers->getId();

        $customer = $this->customer->load($customerId);
        $terms_enable = 1;
        $add_timestamp = $this->date->gmtDate();

        $customerData = $customer->getDataModel();
        $customerData->setCustomAttribute('terms_enable', $terms_enable);
        $customerData->setCustomAttribute('add_timestamp', $add_timestamp);
        $customer->updateData($customerData);
        $customerResource = $this->customerFactory->create();
        $customerResource->saveAttribute($customer, 'terms_enable');
        $customerResource->saveAttribute($customer, 'add_timestamp');
    }
}

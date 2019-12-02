<?php

namespace Artera\Customer\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Check extends Action
{
    protected $_request;
    protected $customer;
    protected $customerRepository;
    protected $customerFactory;
    protected $url;
    protected $date;
    protected $_coreSession;
    protected $_customerSession;
    protected $_cacheFrontendPool;
    protected $_cacheTypeList;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        array $data = [],
        Context $context
    ) {
        $this->_request = $request;
        $this->customer = $customer;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->url = $url;
        $this->date = $date;
        $this->_customerSession = $customerSession;
        $this->_coreSession = $coreSession;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_cacheTypeList = $cacheTypeList;

        parent::__construct($context);
    }

    public function execute()
    {
        $email = $this->_coreSession->getMyValue();
        $login =  $this->_request->getPost('login');
        $customers = $this->customerRepository->get($email, $websiteId = null);
        $customerId = $customers->getId();
        $customer = $this->customer->load($customerId);
        $customerData = $customer->getDataModel();
        $terms_enable = 1;
        $add_timestamp = $this->date->gmtDate();
        $allTypes = array_keys($this->_cacheTypeList->getTypes());

        if ($login['logincustomer_account_login_privacy_included'] == 1) {
            $customerData->setCustomAttribute('terms_enable', $terms_enable);
            $customerData->setCustomAttribute('add_timestamp', $add_timestamp);
            $customer->updateData($customerData);
            $customerResource = $this->customerFactory->create();
            $customerResource->saveAttribute($customer, 'terms_enable');
            $customerResource->saveAttribute($customer, 'add_timestamp');
            $this->_customerSession->setCustomerAsLoggedIn($customer);
            foreach ($allTypes as $type) {
                $this->_cacheTypeList->cleanType($type);
            }
            foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            $redirectionUrl = $this->url->getUrl('customer/account/index');
            header('location: ' . $redirectionUrl);
            exit;
        }
    }
}

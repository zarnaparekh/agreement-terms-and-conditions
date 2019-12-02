<?php

namespace Artera\Customer\Observer;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerLoginObserver implements ObserverInterface
{
    protected $url;
    private $responseFactory;
    protected $customer;
    protected $customerRepository;
    protected $redirect;
    protected $resultRedirectFactory;
    protected $resultFactory;
    protected $_coreSession;
    protected $_customerSession;
    protected $_request;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        ResultFactory $resultFactory
    ) {
        $this->url = $url;
        $this->_request = $request;
        $this->responseFactory = $responseFactory;
        $this->customer = $customer;
        $this->customerRepository = $customerRepository;
        $this->redirect = $redirect;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->resultFactory = $resultFactory;
        $this->_customerSession = $customerSession;
        $this->_coreSession = $coreSession;
    }
    /**
     * @param Observer $observer
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute(Observer $observer)
    {

        if (!$this->_customerSession->isLoggedIn()) {
            $request = $this->_request->getParams();
            $email = $request['login']['username'];
            $this->_coreSession->setMyValue($email);
            $customers = $this->customerRepository->get($email, $websiteId = null);
            $customerId = $customers->getId();
            $customer = $this->customer->load($customerId);
            $terms_enable = $customer->getData()['terms_enable'];
            if ($terms_enable == 0) {
                $redirectionUrl = $this->url->getUrl('tnc/account/index');
                header('location: ' . $redirectionUrl);
                exit;
            }
        } else {
            $redirectionUrl = $this->url->getUrl('customer/account/index');
            $this->responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
            return $this;
        }
    }
}

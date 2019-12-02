<?php

namespace Artera\Customer\Controller\Account;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_coreSession;
    protected $url;
    protected $resultFactory;
    private $redirect;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        ResultFactory $resultFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_coreSession = $coreSession;
        $this->url = $url;
        $this->resultFactory = $resultFactory;
        $this->redirect = $context->getRedirect();
        return parent::__construct($context);
    }

    public function execute()
    {
        $url = $this->_redirect->getRefererUrl();
        if (strpos($url, 'customer/account/login') !== false) {
            return $this->_pageFactory->create();
        } else {
            $norouteUrl = $this->url->getUrl('no-route');
            $this->getResponse()->setRedirect($norouteUrl);
            return;
        }
    }
}

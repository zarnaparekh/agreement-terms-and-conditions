<?php

namespace Artera\Customer\Block;

class Display extends \Magento\Framework\View\Element\Template
{
    protected $_customerUrl;
    protected $_pageFactory;
    protected $_storeManager;
    protected $_page;
    protected $_filterProvider;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Cms\Model\Page $page,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\PageFactory $pageFactory,
        array $data = []
    ) {
        $this->_customerUrl = $customerUrl;
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * Retrieve Page instance
     *
     * @return \Magento\Cms\Model\Page
     */
    public function getPage($identifier)
    {
        if (!$this->hasData('page')) {
            /** @var \Magento\Cms\Model\Page $page */
            $page = $this->_pageFactory->create();
            $page->setStoreId($this->_storeManager->getStore()->getId())->load($identifier, 'identifier');
            $this->setData('page', $page);
        }
        return $this->getData('page');
    }

    public function getContent($identifier)
    {
        return $this->_filterProvider->getPageFilter()->filter($this->getPage($identifier)->getContent());
    }

    public function accepttnc()
    {
        return __('Go through new terms and Conditions and accept them to proceed further.');
    }
}

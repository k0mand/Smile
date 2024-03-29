<?php
/**
 * Smile price request index
 *
 * @category  Smile
 * @package   Smile\Customer
 * @author    Roman Koshyk <romadaaaa@gmail.com>
 */
namespace Smile\Customer\Controller\Adminhtml\RequestPrice;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Smile\Customer\Controller\Adminhtml\RequestPrice
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Smile_Customer::customer_request_price';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Smile_Customer::customer_request_price');
        $resultPage->addBreadcrumb(__('Request price'), __('Request price'));
        $resultPage->addBreadcrumb(__('Request price'), __('Request price'));
        $resultPage->getConfig()->getTitle()->prepend(__('Request price'));
        return $resultPage;
    }
}

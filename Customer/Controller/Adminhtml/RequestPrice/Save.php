<?php
/**
 * Smile price request save
 *
 * @category  Smile
 * @package   Smile\Customer
 * @author    Roman Koshyk <romadaaaa@gmail.com>
 */
namespace Smile\Customer\Controller\Adminhtml\RequestPrice;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Store\Model\Store;
use Smile\Customer\Api\RequestRepositoryInterface;
use Smile\Customer\Model\RequestFactory;
use Smile\Customer\Model\Request;

/**
 * Class Save
 *
 * @package Smile\Customer\Controller\Adminhtml\RequestPrice
 */
class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Smile_Customer::customer_request_price_save';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'customer_request_price/email/admin/email';

    /**
     * Sender name config path
     */
    const XML_PATH_NAME_SENDER = 'customer_request_price/email/admin/name';

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = 'customer_request_price/email/template';

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RequestRepositoryInterface
     */
    private $requestRepository;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param RequestRepositoryInterface $requestRepository
     * @param RequestFactory $requestFactory
     */
    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        DataPersistorInterface $dataPersistor,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        RequestRepositoryInterface $requestRepository,
        RequestFactory $requestFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->dataPersistor = $dataPersistor;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->requestRepository = $requestRepository;
        $this->requestFactory = $requestFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {    
            $postObject = new DataObject();
            $postObject->setData($data);
            $id = $this->getRequest()->getParam('id');
            try {
                if (!$id) {
                    $model = $this->requestFactory->create();
                    $data['id']= null;
                } else {
                    $model = $this->requestRepository->getById($id);
                }
                $model->setData($data);

                if (!empty($data['answer'])) {
                    $this->inlineTranslation->suspend();
                    $storeScope = ScopeInterface::SCOPE_STORE;
                    $transport = $this->transportBuilder
                        ->setTemplateIdentifier('request_admin_email_answer_template')
                        ->setTemplateOptions(
                            [
                                'area' => FrontNameResolver::AREA_CODE,
                                'store' => Store::DEFAULT_STORE_ID,
                            ]
                        )
                        ->setTemplateVars(['data' => $postObject])
                        ->setFrom($this->getSenderData())
                        ->addTo($model->getEmail())
                        ->getTransport();
                    $transport->sendMessage();
                    $this->inlineTranslation->resume();
                    $model->setStatus(Request::STATUS_CLOSED);
                }
                $this->requestRepository->save($model);
                $this->messageManager->addSuccessMessage(__('Email has been sent.'));
                $this->dataPersistor->clear('customer_request_price');
                if ($this->getRequest()->getParam('back'))
                {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getById()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while answer the request.'));
            }
            $this->dataPersistor->set('customer_request_price', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $this->getRequest()->getParam('id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Get sender name and email
     *
     * @return array
     */
    public function getSenderData()
    {
        return [
            'name' => $this->scopeConfig->getValue(static::XML_PATH_NAME_SENDER),
            'email' => $this->scopeConfig->getValue(static::XML_PATH_EMAIL_SENDER)
        ];
    }
}
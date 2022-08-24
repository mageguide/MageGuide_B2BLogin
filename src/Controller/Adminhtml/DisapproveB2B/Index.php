<?php

namespace MageGuide\B2BLogin\Controller\Adminhtml\DisapproveB2B;

class Index extends \Magento\Backend\App\Action
{
    /**
    * Custom Customer attribute options, attribute code: "is_approved"
    */
    /*
    const PENDING_OPTION_ID = '6237';
    const APPROVED_OPTION_ID = '6239';
    const NOT_APPROVED_OPTION_ID = '6238';
    */
    const B2B_WEBSITE_ID = 5;

    /**
     * B2B Store Id
     */
    const B2B_STORE_ID = 6;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context  $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->customerRepository = $customerRepository;
        $this->_scopeConfig       = $scopeConfig;
        $this->_transportBuilder  = $transportBuilder;
        $this->storeManager       = $storeManager;
        $this->logger             = $logger;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $pending_id = \MageGuide\B2BLogin\Observer\Customer\DataObjectLogin::PENDING_OPTION_ID;
        $approve_id = \MageGuide\B2BLogin\Observer\Customer\DataObjectLogin::APPROVED_OPTION_ID;
        $dissaprove_id = \MageGuide\B2BLogin\Observer\Customer\DataObjectLogin::NOT_APPROVED_OPTION_ID;

        $customerId = $this->getRequest()->getParam('id');        
        $customer   = $this->customerRepository->getById($customerId);
        $approved = $customer->getCustomAttribute('am_is_activated');
        if ($approved == null){
            $customer->setCustomAttribute('am_is_activated',$pending_id);
            $this->customerRepository->save($customer);
        }
        if(!empty($customer->getCustomAttribute('am_is_activated')->getValue())
            && $customer->getCustomAttribute('am_is_activated')->getValue() == $dissaprove_id
        ){
            $this->messageManager->addNotice(__('Ο πελάτης έχει ήδη απορριφθεί.'));
        }else if(!empty($customer->getCustomAttribute('am_is_activated')->getValue())){
            $error = false;
            try {
                $customer->setCustomAttribute('am_is_activated', $dissaprove_id);
                $this->customerRepository->save($customer);

                $senderName  = $this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $senderEmail = $this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                $receivingName  = $customer->getFirstName()." ".$customer->getLastName();
                $receivingEmail = $customer->getEmail();

                $emailTempVariables = array('customeremail' => $customer->getEmail());

                $sender = [
                    'name' => $senderName,
                    'email' => $senderEmail
                ];

                $transport = $this->_transportBuilder->setTemplateIdentifier('b2b_customer_disapprove_template')
                    ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => self::B2B_STORE_ID])
                    ->setTemplateVars($emailTempVariables)
                    
                    ->setFrom($sender)
                    ->addTo($receivingEmail,$receivingName)
                    ->setReplyTo($senderEmail)            
                    ->getTransport();
                $transport->sendMessage();
            } catch (\Exception $e) {
                $error = true;
                $this->logger->info('B2B Login DisapproveB2B Error: '.$e->getMessage());
                $this->messageManager->addError(__('Η διαδικασια απόρριψης απέτυχε.'));
            }
            if(!$error){
                $this->messageManager->addSuccess(__('Απορριφθηκε η εγγραφή του πελάτη στο κατάστημα χονδρικής.').' ID: '.$customerId);
            }
        }else{
            $this->messageManager->addError(__('Ο πελάτης δεν έχει δεδομένα έγκρισης.').' ID: '.$customerId);
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customer/index');
        return $resultRedirect;
    }
}
<?php

namespace MageGuide\B2BLogin\Observer\Customer;

class DataObjectLogin implements \Magento\Framework\Event\ObserverInterface
{
    protected $_session;

    const B2B_WEBSITE_ID = 5;

    /**
    * Custom Customer attribute options, attribute code: "is_approved"
    */
    const PENDING_OPTION_ID = 'Εκκρεμή';
    const APPROVED_OPTION_ID = 'Ναι';
    const NOT_APPROVED_OPTION_ID = 'Οχι';

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_session       = $session;
        $this->_customerModel = $customerModel;
        $this->messageManager = $messageManager;
        $this->storeManager   = $storeManager;
        $this->logger         = $logger;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $websiteID          = $this->storeManager->getStore()->getWebsiteId();
        $isCustomerLoggedIn = $this->_session->isLoggedIn();

        if (($websiteID == self::B2B_WEBSITE_ID) && $isCustomerLoggedIn) {
            $customerId  = $observer->getCustomer()->getId();
            $customer    = $this->_customerModel->load($customerId);
            $is_approved = $customer->getData('am_is_activated');
            if($is_approved == self::APPROVED_OPTION_ID){
                //Reviewed and approved, DO NOTHING
            }else if($is_approved == self::PENDING_OPTION_ID){
                //Review pending
                $this->messageManager->addNotice(__('Έχουμε λάβει την αίτηση εγγραφής σας, παρακαλώ αναμείνατε το email ενημέρωσης.'));
                $this->logger->info('B2B pending activation account, tried to login. ID: '.$customerId);

                $this->_session->logout();
            }else if($is_approved == self::NOT_APPROVED_OPTION_ID){
                //Reviewed and rejected 
                $this->messageManager->addError(__('Ευχαριστούμε για την αποστολή της αίτηση εγγραφής, λυπούμαστε αλλα δεν εγκρίθηκε.'));
                $this->logger->info('B2B disapproved account, tried to login. ID: '.$customerId);

                $this->_session->logout();
            }else{
                //Something went wrong 
                $this->messageManager->addError(__('Δεν έχετε ενεργοποιημένο B2B account παρακάλώ επικοινωνήστε με το κατάστημα μας για περισσότερες λεπτομέρειες'));
                $this->logger->info('B2B Login, something went wrong, for customer ID: '.$customerId);

                $this->_session->logout();
            }

            return $this;
        }
    }
}
<?php

namespace MageGuide\B2BLogin\Controller\Adminhtml\ForcePassword;

use Magento\Framework\Encryption\Encryptor;

class Index extends \Magento\Backend\App\Action
{
    /**
    * Custom Customer attribute options, attribute code: "is_approved"
    */

    const B2B_WEBSITE_ID = 5;

    /**
     * B2B Store Id
     */
    const B2B_STORE_ID = 6;

    /**
     * @var \Magento\Framework\Encryption\Encryptor
     */
    private $_encryptor;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    private $_customer;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context  $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        Encryptor $encryptor
    ) {
        parent::__construct($context);
        $this->_customer          = $customer;
        $this->_scopeConfig       = $scopeConfig;
        $this->_transportBuilder  = $transportBuilder;
        $this->_encryptor         = $encryptor;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/password.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $customerId = $this->getRequest()->getParam('id');      
        $customer   = $this->_customer->load($customerId);

        $group = 'AUD';
        if ($customer->getGroupId()) {
            switch ($customer->getGroupId()) {
                case 10:
                    $group = 'AUD1';
                    break;
                case 11:
                    $group = 'AUD2';
                    break;
                case 13:
                    $group = 'AUD3';
                    break;
            }
        }
        
        $email = 'em';
        if ($customer->getEmail()) {
            $email = substr($customer->getEmail(), 0, 2);
        }

        $afm = '5892';
        if ($customer->getCustomAttribute('taxid2') && $customer->getCustomAttribute('taxid2')->getValue()) {
            $afm = substr($customer->getCustomAttribute('taxid2')->getValue(), -4);
        }
        
        $pass = $email . $afm . $group . '!';
        $logger->info('pass: ' . $pass);

        $error = false;
        try {
            $salt = md5(time());
            $version = Encryptor::HASH_VERSION_LATEST;
            $hashed = $this->_encryptor->getHash($pass, $salt, $version);
            $logger->info('hashed: ' . $hashed);
            $customer->setPasswordHash($hashed);
            $customer->save();
        } catch (\Exception $e) {
            $error = true;
            echo $e->getMessage();
            die;
            $this->logger->info('B2B Login Force Password Error: '.$e->getMessage());
            $this->messageManager->addError(__('Η αποθήκευση κωδικού απέτυχε.'));
        }
        if(!$error){
            $this->messageManager->addSuccess(__('Αποθηκεύτηκε νέος κωδικός με επιτυχία. Κωδικός: ') . $pass);
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}

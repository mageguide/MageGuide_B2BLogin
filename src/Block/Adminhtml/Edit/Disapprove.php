<?php

namespace MageGuide\B2BLogin\Block\Adminhtml\Edit;

use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Disapprove extends GenericButton implements ButtonProviderInterface {

    protected $_customerRepository;

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param AccountManagementInterface $customerAccountManagement
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context               $context,
        \Magento\Framework\Registry                         $registry
    ) {
        parent::__construct($context, $registry);
    }

    public function getButtonData()
    { 
        return [
            'label' => __('Disapprove B2B Customer'),
            'id' => 'disapprove-b2b-customer-button',
            'on_click' => sprintf("location.href = '%s';", $this->getDisapproveB2BUrl()),
            'sort_order' => 100
        ];       
    }

     public function getDisapproveB2BUrl()
    {
        $customerId = $this->getCustomerId();

        return $this->getUrl('disapproveb2b/disapproveB2B/index', ['id' => $customerId]);
    }
}
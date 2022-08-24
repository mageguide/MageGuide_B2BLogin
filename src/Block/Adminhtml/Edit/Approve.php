<?php

namespace MageGuide\B2BLogin\Block\Adminhtml\Edit;

use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Approve extends GenericButton implements ButtonProviderInterface {

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param AccountManagementInterface $customerAccountManagement
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry           $registry
    ) {
        parent::__construct($context, $registry);
    }

    public function getButtonData()
    {
        return [
            'label' => __('Approve B2B Customer'),
            'id' => 'approve-b2b-customer-button',
            'on_click' => sprintf("location.href = '%s';", $this->getApproveB2BUrl()),
            'sort_order' => 100
        ];
    }

    public function getApproveB2BUrl()
    {
        $customerId = $this->getCustomerId();

        return $this->getUrl('approveb2b/approveB2B/index', ['id' => $customerId]);
    }   
}
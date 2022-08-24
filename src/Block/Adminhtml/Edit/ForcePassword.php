<?php

namespace MageGuide\B2BLogin\Block\Adminhtml\Edit;

use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ForcePassword extends GenericButton implements ButtonProviderInterface {

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
            'label' => __('Force Password'),
            'id' => 'force-password-button',
            'on_click' => sprintf("location.href = '%s';", $this->getForcePasswordUrl()),
            'sort_order' => 90
        ];
    }

    public function getForcePasswordUrl()
    {
        $customerId = $this->getCustomerId();

        return $this->getUrl('approveb2b/forcepassword/index', ['id' => $customerId]);
    }   
}
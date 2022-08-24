<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageGuide\B2BLogin\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class OrderButtonRemove
 */
class OrderButtonRemove extends \Magento\Customer\Block\Adminhtml\Edit\OrderButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        /*
        $customerId = $this->getCustomerId();
        $data = [];
        if ($customerId && $this->authorization->isAllowed('Magento_Sales::create')) {
            $data = [
                'label' => __('Create Order'),
                'on_click' => sprintf("location.href = '%s';", $this->getCreateOrderUrl()),
                'class' => 'add',
                'sort_order' => 40,
            ];
        }
        return $data;
        */
    }
}

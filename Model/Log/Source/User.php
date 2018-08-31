<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Model\Log\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;

class User implements OptionSourceInterface
{
    /**
     * @var UserCollectionFactory
     */
    protected $userCollectionFactory;

    /**
     * @param UserCollectionFactory $userCollectionFactory
     */
    public function __construct(UserCollectionFactory $userCollectionFactory)
    {
        $this->userCollectionFactory = $userCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        $userCollection = $this->userCollectionFactory->create();
        foreach ($userCollection as $user) {
            /** @var $user \Magento\User\Model\User */
            $options[] = [
                'label' => $user->getUsername(),
                'value' => $user->getUsername()
            ];
        }

        return $options;
    }
}
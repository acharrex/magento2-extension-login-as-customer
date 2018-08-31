<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Cron;

use Shopigo\LoginAsCustomer\Model\ResourceModel\TokenFactory as ResourceTokenFactory;

class DeleteExpiredTokens
{
    /**
     * @var ResourceTokenFactory
     */
    protected $resourceTokenFactory;

    /**
     * Initialize dependencies
     *
     * @param ResourceTokenFactory $resourceLogFactory
     * @return void
     */
    public function __construct(
        ResourceTokenFactory $resourceTokenFactory
    ) {
        $this->resourceTokenFactory = $resourceTokenFactory;
    }

    /**
     * Delete expired tokens
     *
     * @return DeleteExpiredTokens
     */
    public function execute()
    {
        $this->resourceTokenFactory->create()
            ->deleteExpiredTokens();

        return $this;
    }
}

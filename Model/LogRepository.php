<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Reflection\DataObjectProcessor;
use Shopigo\LoginAsCustomer\Api\Data\LogInterface as DataLogInterface;
use Shopigo\LoginAsCustomer\Api\Data\LogInterfaceFactory as DataLogInterfaceFactory;
use Shopigo\LoginAsCustomer\Api\Data\LogSearchResultsInterfaceFactory;
use Shopigo\LoginAsCustomer\Api\LogRepositoryInterface;
use Shopigo\LoginAsCustomer\Model\LogFactory as LogFactory;
use Shopigo\LoginAsCustomer\Model\ResourceModel\Log as ResourceLog;
use Shopigo\LoginAsCustomer\Model\ResourceModel\Log\CollectionFactory as LogCollectionFactory;

class LogRepository implements LogRepositoryInterface
{
    /**
     * @var LogFactory
     */
    protected $logFactory;

    /**
     * @var ResourceLog
     */
    protected $resource;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var LogSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var LogCollectionFactory
     */
    protected $logCollectionFactory;

    /**
     * @var DataLogInterfaceFactory
     */
    protected $dataLogInterfaceFactory;

    /**
     * Initialize dependencies
     *
     * @param LogFactory $logFactory
     * @param ResourceLog $resource
     * @param RequestInterface $request
     * @param RemoteAddress $remoteAddress
     * @param LogSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param LogCollectionFactory $logCollectionFactory
     * @param LogInterfaceFactory $dataLogInterfaceFactory
     */
    public function __construct(
        LogFactory $logFactory,
        ResourceLog $resource,
        RequestInterface $request,
        RemoteAddress $remoteAddress,
        LogSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        LogCollectionFactory $logCollectionFactory,
        DataLogInterfaceFactory $dataLogInterfaceFactory
    ) {
        $this->logFactory = $logFactory;
        $this->resource = $resource;
        $this->request = $request;
        $this->remoteAddress = $remoteAddress;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->logCollectionFactory = $logCollectionFactory;
        $this->dataLogInterfaceFactory = $dataLogInterfaceFactory;
    }

    /**
     * Save log
     *
     * @param DataLogInterface $log
     * @return Log
     * @throws CouldNotSaveException
     */
    public function save(DataLogInterface $log)
    {
        try {
            $this->resource->save($log);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $log;
    }

    /**
     * Generate new log
     *
     * @param int $userId
     * @param string $userName
     * @param int $customerId
     * @param int $storeId
     * @return Log
     */
    public function generate($userId, $username, $customerId, $storeId)
    {
        try {
            $log = $this->logFactory->create()
                ->setData([
                    'user_id'        => $userId,
                    'customer_id'    => $customerId,
                    'store_id'       => $storeId,
                    'user'           => $username,
                    'ip'             => $this->remoteAddress->getRemoteAddress(false),
                    'x_forwarded_ip' => $this->request->getServer('HTTP_X_FORWARDED_FOR')
                ]);

            $this->resource->save($log);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $log;
    }

    /**
     * Load log data by given log identity
     *
     * @param string $logId
     * @return Log
     * @throws NoSuchEntityException
     */
    public function getById($logId)
    {
        $log = $this->logFactory->create();
        $this->resource->load($log, $logId);
        if (!$log->getId()) {
            throw new NoSuchEntityException(__('Log with id "%1" does not exist.', $logId));
        }
        return $log;
    }

    /**
     * Load log data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param SearchCriteriaInterface $criteria
     * @return \Shopigo\LoginAsCustomer\Model\ResourceModel\Log\Collection
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->logCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $searchResults->setTotalCount($collection->getSize());

        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }

        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $logs = [];

        /** @var Log $logModel */
        foreach ($collection as $logModel) {
            $logData = $this->dataLogInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $logData,
                $logModel->getData(),
                'Shopigo\LoginAsCustomer\Api\Data\LogInterface'
            );
            $logs[] = $this->dataObjectProcessor->buildOutputDataArray(
                $logData,
                'Shopigo\LoginAsCustomer\Api\Data\LogInterface'
            );
        }

        $searchResults->setItems($logs);

        return $searchResults;
    }

    /**
     * Delete log
     *
     * @param DataLogInterface $log
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(DataLogInterface $log)
    {
        try {
            $this->resource->delete($log);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete log by given log identity
     *
     * @param string $logId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($logId)
    {
        return $this->delete($this->getById($logId));
    }
}

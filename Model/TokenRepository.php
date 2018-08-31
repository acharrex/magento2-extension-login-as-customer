<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random as MathRandom;
use Magento\Framework\Reflection\DataObjectProcessor;
use Shopigo\LoginAsCustomer\Api\Data\TokenInterface as DataTokenInterface;
use Shopigo\LoginAsCustomer\Api\Data\TokenInterfaceFactory as DataTokenInterfaceFactory;
use Shopigo\LoginAsCustomer\Api\Data\TokenSearchResultsInterfaceFactory;
use Shopigo\LoginAsCustomer\Api\TokenRepositoryInterface;
use Shopigo\LoginAsCustomer\Model\ResourceModel\Token as ResourceToken;
use Shopigo\LoginAsCustomer\Model\ResourceModel\Token\CollectionFactory as TokenCollectionFactory;
use Shopigo\LoginAsCustomer\Model\TokenFactory;

class TokenRepository implements TokenRepositoryInterface
{
    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

    /**
     * @var ResourceToken
     */
    protected $resource;

    /**
     * @var MathRandom
     */
    protected $mathRandom;

    /**
     * @var TokenSearchResultsInterfaceFactory
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
     * @var TokenCollectionFactory
     */
    protected $tokenCollectionFactory;

    /**
     * @var DataTokenInterfaceFactory
     */
    protected $dataTokenInterfaceFactory;

    /**
     * Generate random token
     *
     * @return string
     */
    protected function getRandomToken()
    {
        return $this->mathRandom->getUniqueHash();
    }

    /**
     * Initialize dependencies
     *
     * @param TokenFactory $tokenFactory
     * @param ResourceToken $resource
     * @param MathRandom $mathRandom
     * @param TokenSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param TokenCollectionFactory $tokenCollectionFactory
     * @param DataTokenInterfaceFactory $dataTokenInterfaceFactory
     */
    public function __construct(
        TokenFactory $tokenFactory,
        ResourceToken $resource,
        MathRandom $mathRandom,
        TokenSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        TokenCollectionFactory $tokenCollectionFactory,
        DataTokenInterfaceFactory $dataTokenInterfaceFactory
    ) {
        $this->tokenFactory = $tokenFactory;
        $this->resource = $resource;
        $this->mathRandom = $mathRandom;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->tokenCollectionFactory = $tokenCollectionFactory;
        $this->dataTokenInterfaceFactory = $dataTokenInterfaceFactory;
    }

    /**
     * Save token
     *
     * @param DataTokenInterface $token
     * @return Token
     * @throws CouldNotSaveException
     */
    public function save(DataTokenInterface $token)
    {
        try {
            $this->resource->save($token);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $token;
    }

    /**
     * Generate new token
     *
     * @param int $userId
     * @param int $customerId
     * @return Token
     */
    public function generate($userId, $customerId)
    {
        try {
            $token = $this->tokenFactory->create()
                ->setData([
                    'user_id'     => $userId,
                    'customer_id' => $customerId,
                    'token'       => $this->getRandomToken()
                ]);

            $this->resource->save($token);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $token;
    }

    /**
     * Load token data by given token identity
     *
     * @param string $tokenId
     * @return Token
     * @throws NoSuchEntityException
     */
    public function getById($tokenId)
    {
        $token = $this->tokenFactory->create();
        $this->resource->load($token, $tokenId);
        if (!$token->getId()) {
            throw new NoSuchEntityException(__('Token with id "%1" does not exist.', $tokenId));
        }
        return $token;
    }

    /**
     * Load token data by given token hash
     *
     * @param string $hash
     * @return Token
     * @throws NoSuchEntityException
     */
    public function getByHash($hash)
    {
        $token = $this->tokenFactory->create();
        $this->resource->load($token, $hash, 'token');
        if (!$token->getId()) {
            throw new NoSuchEntityException(__('Token with hash "%1" does not exist.', $hash));
        }
        return $token;
    }

    /**
     * Load token data collection by given search criteria
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

        $collection = $this->tokenCollectionFactory->create();
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

        $tokens = [];

        /** @var Token $tokenModel */
        foreach ($collection as $tokenModel) {
            $tokenData = $this->dataTokenInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $tokenData,
                $tokenModel->getData(),
                'Shopigo\LoginAsCustomer\Api\Data\TokenInterface'
            );
            $tokens[] = $this->dataObjectProcessor->buildOutputDataArray(
                $tokenData,
                'Shopigo\LoginAsCustomer\Api\Data\TokenInterface'
            );
        }

        $searchResults->setItems($tokens);

        return $searchResults;
    }

    /**
     * Delete token
     *
     * @param DataTokenInterface $token
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(DataTokenInterface $token)
    {
        try {
            $this->resource->delete($token);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete token by given token identity
     *
     * @param string $tokenId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($tokenId)
    {
        return $this->delete($this->getById($tokenId));
    }
}

<?php

/*
 * Copyright 2005 - 2020 Centreon (https://www.centreon.com/)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * For more information : contact@centreon.com
 *
 */
declare(strict_types=1);

namespace Centreon\Infrastructure\HostConfiguration\Repository;

use Centreon\Domain\Common\Assertion\Assertion;
use Centreon\Domain\HostConfiguration\Interfaces\HostCategory\HostCategoryReadRepositoryInterface;
use Centreon\Domain\HostConfiguration\Interfaces\HostCategory\HostCategoryWriteRepositoryInterface;
use Centreon\Domain\HostConfiguration\Model\HostCategory;
use Centreon\Domain\Repository\RepositoryException;
use Centreon\Domain\RequestParameters\RequestParameters;
use Centreon\Infrastructure\DatabaseConnection;
use Centreon\Infrastructure\HostConfiguration\Repository\Model\HostCategoryFactoryRdb;
use Centreon\Infrastructure\Repository\AbstractRepositoryDRB;
use Centreon\Infrastructure\RequestParameters\Interfaces\NormalizerInterface;
use Centreon\Infrastructure\RequestParameters\SqlRequestParametersTranslator;

/**
 * This class is designed to represent the MariaDb repository to manage host category.
 *
 * @package Centreon\Infrastructure\HostConfiguration\Repository
 */
class HostCategoryRepositoryRDB extends AbstractRepositoryDRB implements
    HostCategoryReadRepositoryInterface,
    HostCategoryWriteRepositoryInterface
{
    /**
     * @var SqlRequestParametersTranslator
     */
    private $sqlRequestTranslator;

    public function __construct(DatabaseConnection $db, SqlRequestParametersTranslator $sqlRequestTranslator)
    {
        $this->db = $db;
        $this->sqlRequestTranslator = $sqlRequestTranslator;
        $this->sqlRequestTranslator
            ->getRequestParameters()
            ->setConcordanceStrictMode(RequestParameters::CONCORDANCE_MODE_STRICT);
    }

    /**
     * @inheritDoc
     * @throws \Assert\AssertionFailedException
     */
    public function find(int $categoryId): ?HostCategory
    {
        $statement = $this->db->prepare(
            $this->translateDbName('SELECT * FROM `:db`.hostcategories WHERE hc_id = :id')
        );
        $statement->bindValue(':id', $categoryId, \PDO::PARAM_INT);
        $statement->execute();

        if (($result = $statement->fetch(\PDO::FETCH_ASSOC)) !== false) {
            return HostCategoryFactoryRdb::create($result);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        $this->sqlRequestTranslator->setConcordanceArray([
            'id' => 'hc_id',
            'name' => 'hc_name',
            'alias' => 'hc_alias',
            'is_activated' => 'hc_activate',
        ]);
        $this->sqlRequestTranslator->addNormalizer(
            'is_activated',
            new class implements NormalizerInterface
            {
                /**
                 * @inheritDoc
                 */
                public function normalize($valueToNormalize)
                {
                    if (is_bool($valueToNormalize)) {
                        return ($valueToNormalize === true) ? '1' : '0';
                    }
                    return $valueToNormalize;
                }
            }
        );
        $request = $this->translateDbName('SELECT SQL_CALC_FOUND_ROWS * FROM `:db`.hostcategories');

        // Search
        $searchRequest = $this->sqlRequestTranslator->translateSearchParameterToSql();
        $request .= !is_null($searchRequest)
            ? $searchRequest . ' AND level IS NULL'
            : '  WHERE level IS NULL';

        // Sort
        $sortRequest = $this->sqlRequestTranslator->translateSortParameterToSql();
        $request .= !is_null($sortRequest)
            ? $sortRequest
            : ' ORDER BY hc_name ASC';

        // Pagination
        $request .= $this->sqlRequestTranslator->translatePaginationToSql();
        $statement = $this->db->prepare($request);

        foreach ($this->sqlRequestTranslator->getSearchValues() as $key => $data) {
            $type = key($data);
            $value = $data[$type];
            $statement->bindValue($key, $value, $type);
        }
        $statement->execute();

        $result = $this->db->query('SELECT FOUND_ROWS()');
        if ($result !== false && ($total = $result->fetchColumn()) !== false) {
            $this->sqlRequestTranslator->getRequestParameters()->setTotal((int) $total);
        }

        $hostCategories = [];
        while (($record = $statement->fetch(\PDO::FETCH_ASSOC)) !== false) {
            $hostCategories[] = HostCategoryFactoryRdb::create($record);
        }
        return $hostCategories;
    }

    /**
     * @inheritDoc
     * @throws \Assert\AssertionFailedException
     */
    public function findByName(string $hostCategoryName): ?HostCategory
    {
        if (empty($hostCategoryName)) {
            return null;
        }

        $statement = $this->db->prepare(
            $this->translateDbName('SELECT * FROM `:db`.hostcategories WHERE hc_name = :name')
        );

        $statement->bindValue(':name', $hostCategoryName, \PDO::PARAM_STR);
        $statement->execute();

        if (($result = $statement->fetch(\PDO::FETCH_ASSOC)) !== false) {
            return HostCategoryFactoryRdb::create($result);
        }
        return null;
    }
}

<?php

/*
 * Copyright 2005 - 2021 Centreon (https://www.centreon.com/)
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

namespace Centreon\Domain\HostConfiguration;

use Centreon\Domain\HostConfiguration\Exception\HostCategoryException;
use Centreon\Domain\HostConfiguration\Interfaces\HostCategory\HostCategoryReadRepositoryInterface;
use Centreon\Domain\HostConfiguration\Interfaces\HostCategory\HostCategoryServiceInterface;
use Centreon\Domain\HostConfiguration\Model\HostCategory;

/**
 * This class is designed to provide
 *
 * @package Centreon\Domain\HostConfiguration
 */
class HostCategoryService implements HostCategoryServiceInterface
{

    /**
     * @var HostCategoryReadRepositoryInterface
     */
    private $readRepository;

    /**
     * @param HostCategoryReadRepositoryInterface $repository
     */
    public function __construct(HostCategoryReadRepositoryInterface $repository)
    {
        $this->readRepository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function find(int $categoryId): ?HostCategory
    {
        try {
            return $this->readRepository->find($categoryId);
        } catch (\Throwable $ex) {
            throw HostCategoryException::findHostCategoryException($ex, ['id' => $categoryId]);
        }
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        try {
            return $this->readRepository->findAll();
        } catch (\Throwable $ex) {
            throw HostCategoryException::findHostCategoriesException($ex);
        }
    }

    /**
     * @inheritDoc
     */
    public function findByName(string $categoryName): ?HostCategory
    {
        try {
            return $this->readRepository->findByName($categoryName);
        } catch (\Throwable $ex) {
            throw HostCategoryException::findHostCategoryException($ex, ['name' => $categoryName]);
        }
    }
}

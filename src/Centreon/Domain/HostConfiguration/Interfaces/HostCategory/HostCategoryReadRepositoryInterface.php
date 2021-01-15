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

namespace Centreon\Domain\HostConfiguration\Interfaces\HostCategory;

use Centreon\Domain\HostConfiguration\Host;
use Centreon\Domain\HostConfiguration\Model\HostCategory;

/**
 * This interface gathers all the reading operations on the host category repository.
 *
 * @package Centreon\Domain\HostConfiguration\Interfaces
 */
interface HostCategoryReadRepositoryInterface
{
    /**
     * Find a host category.
     *
     * @param int $categoryId Id of the host category to be found
     * @return HostCategory|null
     * @throws \Throwable
     */
    public function find(int $categoryId): ?HostCategory;

    /**
     * Find all host categories.
     *
     * @return HostCategory[]
     * @throws \Throwable
     */
    public function findAll(): array;

    /**
     * Find a host category.
     *
     * @param string $hostCategoryName Name of the host category to be found
     * @return HostCategory|null
     */
    public function findByName(string $hostCategoryName): ?HostCategory;
}

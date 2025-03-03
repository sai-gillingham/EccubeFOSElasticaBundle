<?php

/*
 * This file is part of the FOSElasticaBundle package.
 *
 * (c) FriendsOfSymfony <https://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\ElasticaBundle;

use Elastica\Query\AbstractQuery;
use FOS\ElasticaBundle\Finder\FinderInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Pagerfanta\Pagerfanta;

/**
 * @author Richard Miller <info@limethinking.co.uk>
 *
 * Basic repository to be extended to hold custom queries to be run
 * in the finder
 *
 * @phpstan-import-type TQuery from FinderInterface
 * @phpstan-import-type TOptions from FinderInterface
 */
class Repository
{
    protected PaginatedFinderInterface $finder;

    public function __construct(PaginatedFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @phpstan-param TQuery $query
     * @phpstan-param TOptions $options
     *
     * @param mixed $query
     *
     * @return array<object>
     */
    public function find($query, ?int $limit = null, array $options = [])
    {
        return $this->finder->find($query, $limit, $options);
    }

    /**
     * @phpstan-param TQuery $query
     * @phpstan-param TOptions $options
     *
     * @param mixed $query
     *
     * @return list<HybridResult>
     */
    public function findHybrid($query, ?int $limit = null, array $options = [])
    {
        return $this->finder->findHybrid($query, $limit, $options);
    }

    /**
     * @phpstan-param TQuery $query
     * @phpstan-param TOptions $options
     *
     * @param mixed $query
     *
     * @return Pagerfanta<object>
     */
    public function findPaginated($query, array $options = []): Pagerfanta
    {
        return $this->finder->findPaginated($query, $options);
    }

    /**
     * Find results using ElasticSearch's scroll API
     *
     * @param string|array|AbstractQuery $query
     * @param array<string, mixed> $options
     * @return Pagerfanta<object>
     */
    public function findScrollPaginated(string|array|AbstractQuery $query, array $options = []): Pagerfanta
    {
        return $this->finder->findScrollPaginated($query, $options);
    }

    /**
     * @phpstan-param TQuery $query
     * @phpstan-param TOptions $options
     *
     * @param mixed $query
     *
     * @return Paginator\PaginatorAdapterInterface
     */
    public function createPaginatorAdapter($query, array $options = [])
    {
        return $this->finder->createPaginatorAdapter($query, $options);
    }


    /**
     * Paginator adapter using ElasticSearch's scroll API
     *
     * @param string|array|AbstractQuery $query ElasticSearch query
     * @param array<string, mixed> $options Options for the query
     *
     * @return Paginator\PaginatorAdapterInterface
     */
    public function createScrollPaginatorAdapter(string|array|AbstractQuery $query, array $options = []): Paginator\PaginatorAdapterInterface
    {
        return $this->finder->createScrollPaginatorAdapter($query, $options);
    }

    /**
     * @phpstan-param TQuery $query
     * @phpstan-param TOptions $options
     *
     * @param mixed $query
     *
     * @return Paginator\PaginatorAdapterInterface
     */
    public function createHybridPaginatorAdapter($query, array $options = [])
    {
        return $this->finder->createHybridPaginatorAdapter($query, $options);
    }
}

<?php

/*
 * This file is part of the FOSElasticaBundle package.
 *
 * (c) FriendsOfSymfony <https://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\ElasticaBundle\Finder;

use Elastica\Query;
use Elastica\Result;
use Elastica\SearchableInterface;
use FOS\ElasticaBundle\Paginator\FantaPaginatorAdapter;
use FOS\ElasticaBundle\Paginator\HybridPaginatorAdapter;
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;
use FOS\ElasticaBundle\Paginator\TransformedScrollPaginatorAdapter;
use FOS\ElasticaBundle\Paginator\RawPaginatorAdapter;
use FOS\ElasticaBundle\Paginator\TransformedPaginatorAdapter;
use FOS\ElasticaBundle\Transformer\ElasticaToModelTransformerInterface;
use Pagerfanta\Pagerfanta;

/**
 * Finds elastica documents and map them to persisted objects.
 *
 * @phpstan-import-type TQuery from FinderInterface
 * @phpstan-import-type TOptions from FinderInterface
 */
class TransformedFinder implements PaginatedFinderInterface, PaginatedRawFinderInterface, PaginatedHybridFinderInterface
{
    protected SearchableInterface $searchable;
    protected ElasticaToModelTransformerInterface $transformer;

    public function __construct(SearchableInterface $searchable, ElasticaToModelTransformerInterface $transformer)
    {
        $this->searchable = $searchable;
        $this->transformer = $transformer;
    }

    public function find($query, ?int $limit = null, array $options = []): array
    {
        $results = $this->search($query, $limit, $options);

        return $this->transformer->transform($results);
    }

    public function findHybrid($query, ?int $limit = null, array $options = []): array
    {
        $results = $this->search($query, $limit, $options);

        return $this->transformer->hybridTransform($results);
    }

    public function findRaw($query, ?int $limit = null, array $options = []): array
    {
        return $this->search($query, $limit, $options);
    }

    public function findPaginated($query, array $options = []): Pagerfanta
    {
        $paginatorAdapter = $this->createPaginatorAdapter($query, $options);

        return new Pagerfanta(new FantaPaginatorAdapter($paginatorAdapter));
    }

    public function findHybridPaginated($query, array $options = []): Pagerfanta
    {
        $paginatorAdapter = $this->createHybridPaginatorAdapter($query, $options);

        return new Pagerfanta(new FantaPaginatorAdapter($paginatorAdapter));
    }

    public function findRawPaginated($query, array $options = []): Pagerfanta
    {
        $paginatorAdapter = $this->createRawPaginatorAdapter($query, $options);

        return new Pagerfanta(new FantaPaginatorAdapter($paginatorAdapter));
    }

    /**
     * {@inheritdoc}
     */
    public function findScrollPaginated($query, $options = []): Pagerfanta
    {
        $query = Query::create($query);
        $paginatorAdapter = $this->createScrollPaginatorAdapter($query, $options);

        return new Pagerfanta(new FantaPaginatorAdapter($paginatorAdapter));
    }

    public function createPaginatorAdapter($query, array $options = []): PaginatorAdapterInterface
    {
        $query = Query::create($query);

        return new TransformedPaginatorAdapter($this->searchable, $query, $options, $this->transformer);
    }

    public function createHybridPaginatorAdapter($query, array $options = []): PaginatorAdapterInterface
    {
        $query = Query::create($query);

        return new HybridPaginatorAdapter($this->searchable, $query, $options, $this->transformer);
    }

    public function createRawPaginatorAdapter($query, array $options = []): PaginatorAdapterInterface
    {
        $query = Query::create($query);

        return new RawPaginatorAdapter($this->searchable, $query, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function createScrollPaginatorAdapter(mixed $query, array $options = []) : PaginatorAdapterInterface
    {
        $query = Query::create($query);

        return new TransformedScrollPaginatorAdapter($this->searchable, $query, $options, $this->transformer);
    }

    /**
     * @phpstan-param TQuery $query
     * @phpstan-param TOptions $options
     *
     * @param mixed $query
     *
     * @return Result[]|array<int, Result>
     */
    protected function search($query, ?int $limit = null, array $options = []): array
    {
        $queryObject = Query::create($query);
        if (null !== $limit) {
            $queryObject->setSize($limit);
        }

        return $this->searchable->search($queryObject, $options)->getResults();
    }
}

<?php

namespace FOS\ElasticaBundle\Paginator;

use Elastica\Query;
use Elastica\SearchableInterface;
use FOS\ElasticaBundle\Transformer\ElasticaToModelTransformerInterface;

class TransformedScrollPaginatorAdapter extends RawScrollPaginatorAdapter
{
    private ElasticaToModelTransformerInterface $transformer;

    /**
     * @param SearchableInterface                 $searchable  the object to search in
     * @param Query                               $query       the query to search
     * @param array<string, mixed>                $options     search options
     * @param ElasticaToModelTransformerInterface $transformer the transformer for fetching the results
     */
    public function __construct(SearchableInterface $searchable, Query $query, array $options, ElasticaToModelTransformerInterface $transformer)
    {
        parent::__construct($searchable, $query, $options);

        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults($offset, $length): PartialResultsInterface|RawPartialResults
    {
        return new TransformedPartialResults($this->getElasticaResults($offset, $length), $this->transformer);
    }
}

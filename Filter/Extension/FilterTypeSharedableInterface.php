<?php

namespace Lexik\Bundle\FormFilterBundle\Filter\Extension;

use Lexik\Bundle\FormFilterBundle\Filter\QueryBuilderExecuterInterface;

/**
 * Some filter type can implement this interface to apply the filter to the query.
 */
interface FilterTypeSharedableInterface
{
    /**
     * Add condition(s) to the query builder for the current type.
     *
     * @param  QueryBuilderExecuterInterface $qbe
     */
    public function addShared(QueryBuilderExecuterInterface $qbe);
}

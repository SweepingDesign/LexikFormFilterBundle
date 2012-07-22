<?php

namespace Lexik\Bundle\FormFilterBundle\Tests\Fixtures;

use Lexik\Bundle\FormFilterBundle\Filter\Extension\ApplicableFilterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Expr;

use Doctrine\ORM\QueryBuilder;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;

class TextareaApplicableFilter implements ApplicableFilterInterface
{
    public function supportForm(FormInterface $form)
    {
        $type = $form->getConfig()->getType()->getInnerType();

        return ($type instanceof TextareaType);
    }

    /**
    * Add condition(s) to the query builder for the current type.
    *
    * @param QueryBuilder $queryBuilder
    * @param Expr $e
    * @param string $field
    * @param array $values
    */
    public function applyFilter(QueryBuilder $queryBuilder, Expr $expr, $field, array $values)
    {
        if (!empty($values['value'])) {
            $queryBuilder->andWhere($expr->eq($field, sprintf("'%s'", $values['value'])));
        }
    }

    /**
     * Return service id used to transform values
     *
     * @return string
     */
    public function getTransformerId()
    {
        return 'lexik_form_filter.transformer.default';
    }
}
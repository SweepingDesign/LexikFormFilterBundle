<?php

namespace Lexik\Bundle\FormFilterBundle\Tests\Fixtures\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\QueryBuilder;

use Lexik\Bundle\FormFilterBundle\Filter\QueryBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Expr;
use Lexik\Bundle\FormFilterBundle\Filter\Extension\FilterTypeSharedableInterface;

/**
 * Form filter for tests.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class OptionFilterType extends AbstractType implements FilterTypeSharedableInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', 'filter_text');
        $builder->add('rank', 'filter_number');
    }

    public function getName()
    {
        return 'options_filter';
    }

    public function addShared(QueryBuilderExecuterInterface $qbe)
    {
        $qbe->addOnce($qbe->getAlias().'.options', 'opt', function(QueryBuilder $queryBuilder, $alias, $joinAlias, Expr $expr) {
            $queryBuilder->leftJoin($alias . '.options', 'opt');
        });
    }
}
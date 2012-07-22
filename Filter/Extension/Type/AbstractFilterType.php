<?php

namespace Lexik\Bundle\FormFilterBundle\Filter\Extension\Type;

use Lexik\Bundle\FormFilterBundle\Filter\Extension\FilterTypeInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Expr;

use Doctrine\ORM\QueryBuilder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Abstract Filter type
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
abstract class AbstractFilterType extends AbstractType implements FilterTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ($options['apply_filter'] instanceof \Closure || is_callable($options['apply_filter'])) {
            $builder->setAttribute('apply_filter', $options['apply_filter']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
             'required'     => false,
             'apply_filter' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilter(QueryBuilder $queryBuilder, Expr $expr, $field, array $values)
    {
        if (!empty($values['value'])) {
            $queryBuilder->andWhere($expr->eq(sprintf('%s.%s', $values['alias'], $field), $value));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTransformerId()
    {
        return 'lexik_form_filter.transformer.default';
    }
}

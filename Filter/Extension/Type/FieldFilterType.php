<?php

namespace Lexik\Bundle\FormFilterBundle\Filter\Extension\Type;

/**
 * Base filter type.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class FieldFilterType extends AbstractFilterType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'filter_field';
    }
}

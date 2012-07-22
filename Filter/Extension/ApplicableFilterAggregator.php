<?php

namespace Lexik\Bundle\FormFilterBundle\Filter\Extension;

use Symfony\Component\Form\FormInterface;

class ApplicableFilterAggregator
{
    /**
     * @var array
     */
    protected $applicableFilters;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->applicableFilters = array();
    }

    /**
     * Add an applicable filter.
     *
     * @param mixed $id
     * @param ApplicableFilterInterface $applicableFilter
     */
    public function add($id, ApplicableFilterInterface $applicableFilter)
    {
        if (!isset($this->applicableFilters[$id])) {
            $this->applicableFilters[$id] = $applicableFilter;
        }
    }

    /**
     * Returns an applicable filter by its id.
     *
     * @param mixed $id
     * @throws \RuntimeException
     * @return ApplicableFilterInterface
     */
    public function get($id)
    {
        if (!isset($this->applicableFilters[$id])) {
            throw new \RuntimeException(sprintf('No applicable filter found for id "%s"', $id));
        }

        return $this->applicableFilters[$id];
    }

    /**
     * Returns the first applicable filter's id supporting the given form.
     *
     * @param FormInterface $form
     * @return mixed
     */
    public function findApplicableFilterFor(FormInterface $form)
    {
        $i = 0;
        $id = null;
        $ids = array_keys($this->applicableFilters);

        while($i<count($this->applicableFilters) && null === $id) {
            $id = $this->applicableFilters[$ids[$i]]->supportForm($form) ? $ids[$i] : null;
            $i++;
        }

        return $id;
    }
}
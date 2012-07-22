<?php

namespace Lexik\Bundle\FormFilterBundle\Filter\Extension;

use Symfony\Component\Form\FormInterface;

interface ApplicableFilterInterface extends FilterTypeInterface
{
    /**
     * Returns true if the given form is supported.
     *
     * @param FormInterface $form
     * @return boolean
     */
    public function supportForm(FormInterface $form);
}
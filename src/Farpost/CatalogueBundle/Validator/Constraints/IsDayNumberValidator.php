<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 10/07/15
 * Time: 11:09
 */

namespace Farpost\CatalogueBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsDayNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/^[1-7]$/', $value, $matches)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
        }
    }
}
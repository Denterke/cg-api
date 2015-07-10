<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 10/07/15
 * Time: 11:06
 */

namespace Farpost\CatalogueBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class IsDayNumber
 * @package Farpost\CatalogueBundle\Validator\Constraints
 *
 * @Annotation
 */
class IsDayNumber extends Constraint
{
    public $message = 'The number "%string%" not in range [1..7]';

}
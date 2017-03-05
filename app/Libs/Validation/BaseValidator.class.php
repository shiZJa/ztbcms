<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Libs\Validation;

use Respect\Validation\Validator;

abstract class BaseValidator {

    /**
     * 创建Validator
     *
     * @return Validator
     */
    public abstract function makeValidator();

}
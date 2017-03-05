<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace System\Validator;

use Libs\Validation\BaseValidator;
use Respect\Validation\Validator;

/**
 * 示例Validator
 */
class DemoValidator extends BaseValidator {


    /**
     * 创建Validator
     *
     * @return Validator
     */
    public function makeValidator() {
        $v = Validator::key('username', Validator::length(3, 10));

        return $v;
    }
}
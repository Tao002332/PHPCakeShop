<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class OrderStatusCode extends Enum
{
    const USER_DONT_RECEIVER =   -3;
    const NOT_PAY =   -2;
    const USER_CANCEL =   -1;
    const WAIT_DELIVERY = 0;
    const DELIVERY = 1;
    const USER_CONFIRM= 2;
}

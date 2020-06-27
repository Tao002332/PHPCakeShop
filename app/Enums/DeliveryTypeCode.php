<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class DeliveryTypeCode extends Enum
{
    const DELIVERY_IN_LOGISTICES =  0;
    const SELF_DELIVERY =  1;
}

<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class OrderDataFlagCode extends Enum
{
    const DELETED =   0;
    const VALID =   1;
}

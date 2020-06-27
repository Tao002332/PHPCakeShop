<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ProductDataFlagCode extends Enum
{
    const PULL_OFF =   0;
    const PUT_ON =   1;
}

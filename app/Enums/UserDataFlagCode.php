<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class UserDataFlagCode extends Enum
{
    const NOT_ACTIVE =   -1;
    const FORBID = 0;
    const OK =   1;
    const DELETED =   2;
}

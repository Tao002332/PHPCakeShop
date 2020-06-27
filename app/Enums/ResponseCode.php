<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ResponseCode extends Enum
{
    const OK =   20000;
    const NOT_PARAMS =   50001;
    const  EXCUTE_ERROR= 50002;
    const  UPLOAD_ERROR= 50003;
    const  USERNAME_PASSWORD_ERROR= 40001;
    const  NOT_USER_AUTH= 40002;
    const  NOT_ADMIN_AUTH= 40003;
    const  QUERY_CONDITION_ERROR= 40004;
    const  INSERT_ERROR= 40005;
    const  UPDATE_ERROR= 40006;
    const  DELETE_ERROR= 40007;
    const  NEED_RELOGIN= 40008;
    const  PARAMS_CHECK_ERROR= 40009;
    const  USER_AUTH_ERROR= 40010;
    const  USER_FORBID= 40011;
    const  NOT_ACTIVE= 40012;


}

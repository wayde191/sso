<?php
Class IhCode extends CI_Model
{
    // Server
    const request_success = 1;
    const request_fails = 0;

    // User
    const field_required = 900;
    const password_not_equal = 901;
    const email_has_been_taken = 902;
    const phone_number_has_been_taken = 903;
    const password_wrong = 904;
    const user_not_exist = 905;

    // SQL
    const sql_error = 1001;

    // Token
    const token_not_correct = 1201;

    //Security
    const security_code_error = 1101;
    const i_hakula_secret_key = 'iHakulaStartAt2010';
    const i_hakula_security_code = 'iHakulaSecurityCode2016';
}
?>
<?php
/**
 * @author Klayton R. Massango <klayton0304massango@gmail.com>
 * @license http://mit-license.org/
 * @link https://github.com/Klayton258
 * @copyright Klayton Massango
 */

return [

    'public_key' => env('MPESA_PUBLIC_KEY'),
    'api_host' => env('MPESA_API_HOST'),
    'api_key' => env('MPESA_API_KEY'),
    'origin' => env('MPESA_ORIGIN'),
    'service_provider_code' => env('MPESA_SERVICE_PROVIDER_CODE'),
    /**
     * C2B params
     * @param API PORT
     * @param API URL
     */
    'c2b_api_port'=>'18352',
    'c2b_api_path'=>'/ipg/v1x/c2bPayment/singleStage/',
    //========================================

    /**
     * B2C params
     * @param API PORT
     * @param API URL
     */
    'b2c_api_port'=>'18345',
    'b2c_api_path'=>'/ipg/v1x/b2cPayment/',
    //========================================

    /**
     * B2B params
     * @param API PORT
     * @param API URL
     */
    'b2b_api_port'=>'18349',
    'b2b_api_path'=>'/ipg/v1x/b2bPayment/',
    //========================================

];

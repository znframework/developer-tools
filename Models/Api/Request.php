<?php namespace Api;
/**
 * ZN PHP Web Framework
 * 
 * "Simplicity is the ultimate sophistication." ~ Da Vinci
 * 
 * @package ZN
 * @license MIT [http://opensource.org/licenses/MIT]
 * @author  Ozan UYKUN [ozan@znframework.com]
 */

use Method;
use Restful;
use Masterpage;
use ZN\Model;

class Request extends Model
{
    public static function send()
    {
        $type = Method::post('type');

        if( $data = Method::post('data') )
        {
            $explode = explode(',', $data);
            $newData = [];

            foreach( $explode as $value )
            {
                $valueEx = explode(':', trim(str_replace(EOL, '', $value)));

                if( isset($valueEx[1]) )
                {
                    $newData[$valueEx[0]] = $valueEx[1];
                }
            }

            Restful::data($newData);
        }

        if( $ssl = Method::post('sslVerifyPeer') )
        {
            Restful::sslVerifyPeer((bool) $ssl);
        }

        Masterpage::pdata(['results' => Restful::$type(Method::post('url'))]);

        $infos = Restful::info('all');

        Masterpage::pdata(['infos' => ! empty($infos) ? $infos : Restful::info()]);
    }
}
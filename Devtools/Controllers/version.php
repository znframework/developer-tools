<?php namespace Project\Controllers;

use Restful;
use Import;
use Redirect;

class Version extends Controller
{
    /**
     * Notes Page
     */
    public function notes(String $params = NULL)
    {
        if( ! $versions = Restful::post('https://api.znframework.com/statistics/versions') )
        {
            Redirect::location();
        }

        Import::handload('Functions');
        
        $pdata['notes'] = $versions;

        Masterpage::page('versions-notes');

        Masterpage::pdata($pdata);
    }
}

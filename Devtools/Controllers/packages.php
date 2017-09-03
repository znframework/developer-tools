<?php namespace Project\Controllers;

//------------------------------------------------------------------------------------------------------------
// GENERATE
//------------------------------------------------------------------------------------------------------------
//
// Author   : ZN Framework
// Site     : www.znframework.com
// License  : The MIT License
// Copyright: Copyright (c) 2012-2016, znframework.com
//
//------------------------------------------------------------------------------------------------------------

use Restful, JC, Method, Http, Processor, File;

class Packages extends Controller
{
    protected $downloadFileName = FILES_DIR . 'DownloadPackageList.txt';

    //--------------------------------------------------------------------------------------------------------
    // Controller
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function main(String $params = NULL)
    {
        if( Method::post('search') )
        {
            $list = [];

            if( File::exists($this->downloadFileName) )
            {
                $list = explode(EOL, File::read($this->downloadFileName));
            }

            $data = Restful::get('https://packagist.org/search.json?q=' . Method::post('name') );

            $this->masterpage->pdata['result'] = $data->results;
            $this->masterpage->pdata['list']   = $list;
         }

        $this->masterpage->page = 'package';
    }

    public function download()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $name = Method::post('name');

        exec('composer require ' . $name, $response, $return);

        File::append($this->downloadFileName, $name . EOL);
    }
}

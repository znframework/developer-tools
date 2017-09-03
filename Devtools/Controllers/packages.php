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

use Restful, JC, Method, Http, Processor, File, Arrays, URI;

class Packages extends Controller
{
    protected $downloadFileName = FILES_DIR . 'DownloadPackageList.txt';

    protected $list = [];

    public function __construct()
    {
        parent::__construct();

        if( File::exists($this->downloadFileName) )
        {
            $this->list = Arrays::deleteElement(explode(EOL, File::read($this->downloadFileName)), '');
        }
    }

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
            $data = Restful::get('https://packagist.org/search.json?q=' . Method::post('name') );

            $this->masterpage->pdata['result'] = $data->results;
         }

        $this->masterpage->pdata['list'] = $this->list;

        $this->masterpage->page = 'package';
    }

    //--------------------------------------------------------------------------------------------------------
    // Delete
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function delete()
    {
        $newList = Arrays::deleteElement($this->list, URI::get('delete', 2, true));

        File::write($this->downloadFileName, implode(EOL, $newList));

        redirect('packages');
    }

    //--------------------------------------------------------------------------------------------------------
    // Ajax Download
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
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

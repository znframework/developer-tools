<?php namespace Project\Controllers;

//------------------------------------------------------------------------------------------------------------
// HOME
//------------------------------------------------------------------------------------------------------------
//
// Author   : ZN Framework
// Site     : www.znframework.com
// License  : The MIT License
// Copyright: Copyright (c) 2012-2016, znframework.com
//
//------------------------------------------------------------------------------------------------------------

use Restful, Method, Validation, File, Session;

class Home extends Controller
{
    //--------------------------------------------------------------------------------------------------------
    // Main
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function main(String $params = NULL)
    {
        if( Method::post('create') )
        {
            Validation::rules('project', ['alpha'], 'Project Name');

            if( ! $error = Validation::error('string') )
            {
                $source = FILES_DIR . 'Default.zip';
                $target = PROJECTS_DIR . Method::post('project');


                File::zipExtract($source, $target);

                $this->masterpage->pdata['success'] = LANG['success'];
            }
            else
            {
                $this->masterpage->pdata['error'] = $error;
            }
        }

        if( ! $return = Session::select('return') )
        {
            $return = Restful::get('https://www.znframework.com/api');
            Session::insert('return', $return);
        }

        $this->masterpage->page  = 'dashboard';
        $this->masterpage->pdata['return'] = $return;
    }

    //--------------------------------------------------------------------------------------------------------
    // Docs
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function docs(String $params = NULL)
    {
        $this->masterpage->page  = 'docs';
    }

    //--------------------------------------------------------------------------------------------------------
    // Lang
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function lang($lang = NULL)
    {
        setlang($lang);
        redirect((string) prevUrl());
    }
}

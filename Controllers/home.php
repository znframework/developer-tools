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

use Restful, Method, Validation, File, Session, Json;

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

                redirect(currentPath(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = $error;
            }
        }

        if( ! $return = Session::select('return') )
        {
            $return = Restful::get('https://api.znframework.com/statistics');

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
        $docs = FILES_DIR . 'docs.json';

        if( Method::post('refresh') )
        {
            File::delete($docs);
            clearstatcache();
        }

        if( ! File::exists($docs) )
        {
            $return = Restful::get('https://api.znframework.com/docs');

            if( ! empty($return) )
            {
                File::write($docs, Json::encode($return));
            }
        }
        else
        {
            $return = Json::decode(File::read($docs));
        }

        \Import::handload('Functions');

        $this->masterpage->plugin['name'] = array_merge(\Config::get('Masterpage', 'plugin')['name'], [
            'Dashboard/highlight/styles/agate.css',
            'Dashboard/highlight/highlight.pack.js'
        ]);

        $this->masterpage->pdata['docs'] = $return;

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

    //--------------------------------------------------------------------------------------------------------
    // Lang
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function project($project = NULL)
    {
        Session::insert('project', $project);
        redirect((string) prevUrl());
    }
}

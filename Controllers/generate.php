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

use Method, Arrays, Generate as Gen;
use Validation, Folder;

class Generate extends Controller
{
    //--------------------------------------------------------------------------------------------------------
    // Controller
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function controller(String $params = NULL)
    {
        if( Method::post('generate') )
        {
            Validation::rules('controller', ['required', 'alpha'], 'Controller Name');

            if( ! $error = Validation::error('string') )
            {
                $functions = explode(',', Method::post('functions'));

                if( ! Arrays::valueExists($functions, 'main') )
                {
                    $functions = Arrays::addFirst($functions, 'main');
                }

                $status = Gen::controller(Method::post('controller'),
                [
                    'application' => SELECT_PROJECT,
                    'namespace'   => 'Project\Controllers',
                    'extends'     => 'Controller',
                    'functions'   => $functions
                ]);

                redirect(currentPath(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = $error;
            }
        }


        $this->masterpage->page  = 'generate';
        $this->masterpage->pdata['content'] = 'controller';
        $this->masterpage->pdata['files']   = Folder::files(PROJECTS_DIR . SELECT_PROJECT . DS . 'Controllers/', 'php');
    }

    //--------------------------------------------------------------------------------------------------------
    // Model
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function model(String $params = NULL)
    {
        if( Method::post('generate') )
        {
            Validation::rules('model', ['required', 'alpha'], 'Controller Name');

            if( ! $error = Validation::error('string') )
            {
                $functions = explode(',', Method::post('functions'));

                $status = Gen::model(Method::post('model'),
                [
                    'application' => SELECT_PROJECT,
                    'namespace'   => Method::post('namespace'),
                    'extends'     => Method::post('extends'),
                    'functions'   => $functions
                ]);

                redirect(currentPath(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = $error;
            }
        }

        $this->masterpage->page  = 'generate';
        $this->masterpage->pdata['content'] = 'model';
        $this->masterpage->pdata['files']   = Folder::files(PROJECTS_DIR . SELECT_PROJECT . DS . 'Models/', 'php');
    }

    //--------------------------------------------------------------------------------------------------------
    // Migration
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function migration(String $params = NULL)
    {
        if( Method::post('generate') )
        {
            Validation::rules('migration', ['required', 'alpha'], 'Migration Name');

            if( ! $error = Validation::error('string') )
            {
                $path = PROJECTS_DIR . SELECT_PROJECT . DS . 'Models/Migrations/';

                \Migration::path($path)->create(Method::post('migration'), (int) Method::post('version'));

                redirect(currentPath(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = $error;
            }
        }

        $this->masterpage->page  = 'generate';
        $this->masterpage->pdata['content'] = 'migration';
        $this->masterpage->pdata['files']   = Folder::files(PROJECTS_DIR . SELECT_PROJECT . DS . 'Models/Migrations/', 'php');
    }
}

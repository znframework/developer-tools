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
use Validation, Folder, File, Config;

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
            Validation::rules('controller', ['required', 'alpha'], LANG['controllerName']);

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

                redirect(currentUri(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = $error;
            }
        }

        $path = 'Controllers/';

        $this->masterpage->page                = 'generate';
        $this->masterpage->pdata['content']    = 'controller';
        $this->masterpage->pdata['fullPath']   = $fullPath = SELECT_PROJECT_DIR . $path;
        $this->masterpage->pdata['deletePath'] = $path;
        $this->masterpage->pdata['files']      = Folder::files($fullPath, 'php');
    }

    //--------------------------------------------------------------------------------------------------------
    // Command
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function command(String $params = NULL)
    {
        if( IS_CONTAINER )
        {
            redirect();
        }

        if( Method::post('generate') )
        {
            Validation::rules('command', ['required', 'alpha'], LANG['commandName']);

            if( ! $error = Validation::error('string') )
            {
                $functions = explode(',', Method::post('functions'));

                if( ! Arrays::valueExists($functions, 'main') )
                {
                    $functions = Arrays::addFirst($functions, 'main');
                }

                $status = Gen::command(Method::post('command'),
                [
                    'application' => SELECT_PROJECT,
                    'namespace'   => 'Project\Commands',
                    'extends'     => 'Command',
                    'functions'   => $functions
                ]);

                redirect(currentUri(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = $error;
            }
        }

        $path = 'Commands/';

        $this->masterpage->page                = 'generate';
        $this->masterpage->pdata['content']    = 'command';
        $this->masterpage->pdata['fullPath']   = $fullPath = SELECT_PROJECT_DIR . $path;
        $this->masterpage->pdata['deletePath'] = $path;
        $this->masterpage->pdata['files']      = Folder::files($fullPath, 'php');
    }

    //--------------------------------------------------------------------------------------------------------
    // Command
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function route(String $params = NULL)
    {
        if( IS_CONTAINER )
        {
            redirect();
        }

        $path = 'Routes/';

        $this->masterpage->pdata['fullPath']   = $fullPath = SELECT_PROJECT_DIR . $path;

        if( Method::post('generate') )
        {
            Validation::rules('route', ['required', 'alpha'], LANG['routeName']);

            if( ! $error = Validation::error('string') )
            {
                $functions = explode(',', Method::post('functions'));

                File::create($fullPath . suffix(Method::post('route'), '.php'));

                redirect(currentUri(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = $error;
            }
        }

        $this->masterpage->page                = 'generate';
        $this->masterpage->pdata['content']    = 'route';
        $this->masterpage->pdata['deletePath'] = $path;
        $this->masterpage->pdata['files']      = Folder::files($fullPath, 'php');
    }

    //--------------------------------------------------------------------------------------------------------
    // Command
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function config(String $params = NULL)
    {
        if( IS_CONTAINER )
        {
            redirect();
        }

        $path = 'Config/';

        $this->masterpage->pdata['fullPath']   = $fullPath = SELECT_PROJECT_DIR . $path;

        if( Method::post('generate') )
        {
            Validation::rules('config', ['required', 'alpha'], LANG['configName']);

            if( ! $error = Validation::error('string') )
            {
                $functions = explode(',', Method::post('functions'));

                File::create($fullPath . suffix(Method::post('config'), '.php'));

                redirect(currentUri(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = $error;
            }
        }

        $this->masterpage->page                = 'generate';
        $this->masterpage->pdata['content']    = 'config';
        $this->masterpage->pdata['deletePath'] = $path;
        $this->masterpage->pdata['files']      = Folder::files($fullPath, 'php');
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
        if( IS_CONTAINER )
        {
            redirect();
        }

        if( Method::post('generate') )
        {
            Validation::rules('model', ['required', 'alpha'], LANG['modelName']);

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

                redirect(currentUri(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = $error;
            }
        }

        $path = 'Models/';

        $this->masterpage->page  = 'generate';
        $this->masterpage->pdata['content'] = 'model';
        $this->masterpage->pdata['deletePath'] = $path;

        $this->masterpage->pdata['fullPath']   = $modelFullPath = SELECT_PROJECT_DIR . $path;

        if( Folder::exists($modelFullPath) )
        {
            $files = Folder::files($modelFullPath, 'php');
        }

        $this->masterpage->pdata['files'] = $files ?? [];
    }

    //--------------------------------------------------------------------------------------------------------
    // Model
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function view(String $params = NULL)
    {
        $templates = Folder::files(TEMPLATES_DIR, 'php');
        $templates = Arrays::combine($templates, $templates);
        $templates['none'] = 'none';


        define('VIEW_TEMPLATES', $templates);

        if( Method::post('generate') )
        {
            Validation::rules('view', ['required', 'alpha'], LANG['viewName']);

            if( ! $error = Validation::error('string') )
            {
                $viewName = Method::post('view');

                if( Method::post('type') === 'Wizard' )
                {
                    $viewName = suffix($viewName, '.wizard');
                }

                $viewPath = SELECT_PROJECT_DIR . 'Views/' . suffix($viewName, '.php');
                $template = Method::post('template');



                if( $template === 'none' )
                {
                    $content = '';
                }
                else
                {
                    $content = File::read(TEMPLATES_DIR.$template);
                }

                File::write($viewPath, $content);

                redirect(currentUri(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = $error;
            }
        }

        $path = 'Views/';

        $this->masterpage->page  = 'generate';
        $this->masterpage->pdata['content'] = 'view';
        $this->masterpage->pdata['deletePath'] = $path;

        $this->masterpage->pdata['fullPath']   = $modelFullPath = SELECT_PROJECT_DIR . $path;

        if( Folder::exists($modelFullPath) )
        {
            $files = Folder::files($modelFullPath, 'php');
        }

        $this->masterpage->pdata['files'] = $files ?? [];
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
        if( IS_CONTAINER )
        {
            redirect();
        }

        if( Method::post('generate') )
        {
            Validation::rules('migration', ['required', 'alpha'], LANG['migrationName']);

            if( ! $error = Validation::error('string') )
            {
                $path = PROJECTS_DIR . SELECT_PROJECT . DS . 'Models/Migrations/';

                \Migration::path($path)->create(Method::post('migration'), (int) Method::post('version'));

                redirect(currentUri(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = $error;
            }
        }

        $path = 'Models/Migrations/';

        $this->masterpage->page  = 'generate';
        $this->masterpage->pdata['content'] = 'migration';
        $this->masterpage->pdata['deletePath'] = $path;

        $this->masterpage->pdata['fullPath']   = $modelFullPath = SELECT_PROJECT_DIR . $path;

        if( Folder::exists($modelFullPath) )
        {
            $files = Folder::files($modelFullPath, ['php', 'dir']);
        }

        $this->masterpage->pdata['files'] = $files ?? [];
    }
}

<?php namespace Project\Controllers;

use Method, Arrays, Generate as Gen;
use Validation;

class Generate extends Controller
{
    public function controller(String $params = NULL)
    {
        if( Method::post('generate') )
        {
            Validation::rules('controller', ['required', 'alnum'], 'Controller Name');

            if( ! $error = Validation::error('string') )
            {
                $functions = explode(',', Method::post('functions'));

                if( ! Arrays::valueExists($functions, 'main') )
                {
                    $functions = Arrays::addFirst($functions, 'main');
                }

                $status = Gen::controller(Method::post('controller'),
                [
                    'application' => Method::post('projects'),
                    'namespace'   => 'Project\Controllers',
                    'extends'     => 'Controller',
                    'functions'   => $functions
                ]);

                $this->masterpage->pdata['success'] = LANG['success'];
            }
            else
            {
                $this->masterpage->pdata['error'] = $error;
            }
        }

        $this->masterpage->page  = 'generate';
        $this->masterpage->pdata['content'] = 'controller';
    }

    public function model(String $params = NULL)
    {
        if( Method::post('generate') )
        {
            Validation::rules('model', ['required', 'alnum'], 'Controller Name');

            if( ! $error = Validation::error('string') )
            {
                $functions = explode(',', Method::post('functions'));

                $status = Gen::model(Method::post('model'),
                [
                    'application' => Method::post('projects'),
                    'namespace'   => Method::post('namespace'),
                    'extends'     => Method::post('extends'),
                    'functions'   => $functions
                ]);

                $this->masterpage->pdata['success'] = LANG['success'];
            }
            else
            {
                $this->masterpage->pdata['error'] = $error;
            }
        }

        $this->masterpage->page  = 'generate';
        $this->masterpage->pdata['content'] = 'model';
    }

    public function migration(String $params = NULL)
    {
        if( Method::post('generate') )
        {
            Validation::rules('migration', ['required', 'alnum'], 'Migration Name');

            if( ! $error = Validation::error('string') )
            {
                $path = PROJECTS_DIR . Method::post('projects') . DS . 'Models/Migrations/';

                \Migration::path($path)->create(Method::post('migration'), (int) Method::post('version'));

                $this->masterpage->pdata['success'] = LANG['success'];
            }
            else
            {
                $this->masterpage->pdata['error'] = $error;
            }
        }

        $this->masterpage->page  = 'generate';
        $this->masterpage->pdata['content'] = 'migration';
    }
}

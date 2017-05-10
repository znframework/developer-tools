<?php namespace Project\Controllers;

//------------------------------------------------------------------------------------------------------------
// INITIALIZE
//------------------------------------------------------------------------------------------------------------
//
// Author   : ZN Framework
// Site     : www.znframework.com
// License  : The MIT License
// Copyright: Copyright (c) 2012-2016, znframework.com
//
//------------------------------------------------------------------------------------------------------------

use Folder, Arrays, Form, Config, Route, Validation, Session;

class Initialize extends Controller
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
        $ips = Config::get('Dashboard', 'ip');

        if( ! Arrays::valueExists($ips, ipv4()) )
        {
            Route::redirectInvalidRequest();
        }

        define('LANG', lang('Dashboard'));
        define('DASHBOARD_VERSION', '1.0.0');

        $projects = Folder::files(PROJECTS_DIR, 'dir');
        $projects = Arrays::deleteElement($projects, CURRENT_PROJECT);
        $projects = Arrays::combine($projects, $projects);
        $default  = PROJECTS_CONFIG['directory']['default'];

        $currentProject = Session::select('project');

        define('PROJECT_LIST', $projects);
        define('SELECT_PROJECT', ! empty($currentProject) ? $currentProject : DEFAULT_PROJECT);
        define('LANGUAGES', ['EN', 'TR']);

        $menus =
        [
            'home'          => ['icon' => 'home',       'href' => 'home/main'],
            'controllers'   => ['icon' => 'gears',      'href' => 'generate/controller'],
            'models'        => ['icon' => 'database',   'href' => 'generate/model'],
            'migrations'    => ['icon' => 'cubes',      'href' => 'generate/migration'],
            'sqlConverter'  => ['icon' => 'refresh',    'href' => 'system/converter'],
            'documentation' => ['icon' => 'book',       'href' => 'home/docs'],
            'systemLogs'    => ['icon' => 'cogs',       'href' => 'system/log'],
            'systemInfo'    => ['icon' => 'info',       'href' => 'system/info']
        ];

        define('MENUS', $menus);
    }
}

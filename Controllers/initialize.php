<?php namespace Project\Controllers;

use Folder, Arrays, Form, Config, Route;

class Initialize extends Controller
{
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
        $projects = Arrays::combine($projects, $projects);
        $default  = PROJECTS_CONFIG['directory']['default'];
        $select   = Form::class('form-control')->select('projects', $projects, $default);

        define('PROJECT_LIST', $projects);
        define('PROJECTS_SELECT', $select);
        define('LANGUAGES', ['EN', 'TR']);

        $menus =
        [
            'home'          => ['icon' => 'home',       'href' => 'home/main'],
            'controllers'   => ['icon' => 'gears',      'href' => 'generate/controller'],
            'models'        => ['icon' => 'database',   'href' => 'generate/model'],
            'migrations'    => ['icon' => 'cubes',      'href' => 'generate/migration'],
            'documentation' => ['icon' => 'book',       'href' => 'home/docs'],
            'systemInfo'   => ['icon' => 'info',       'href' => 'info/main']
        ];

        define('MENUS', $menus);
    }
}

<?php namespace Project\Controllers;

use Method, Folder, File, Html;

class System extends Controller
{
    public function info(String $params = NULL)
    {
        $this->masterpage->page  = 'info';
    }

    public function log(String $params = NULL)
    {
        if( Method::post('show') )
        {
            $project = Method::post('projects');

            $path = PROJECTS_DIR . $project . DS . 'Storage/Logs/';

            $files = Folder::files($path, 'log');

            $logs = '<hr>';

            foreach( $files as $file )
            {
                $logs .= Html::strong('File: ' . $file) . Html::br(1);
                $logs .= Html::parag(str_replace('IP', '<br>IP', File::read($path . $file))) . '<hr>';
            }
        }

        $this->masterpage->pdata['logs'] = $logs ?? LANG['notFound'] . '!';
        $this->masterpage->page  = 'logs';
    }
}

<?php namespace Project\Controllers;

//------------------------------------------------------------------------------------------------------------
// SYSTEM
//------------------------------------------------------------------------------------------------------------
//
// Author   : ZN Framework
// Site     : www.znframework.com
// License  : The MIT License
// Copyright: Copyright (c) 2012-2016, znframework.com
//
//------------------------------------------------------------------------------------------------------------

use Method, Folder, File, Html, Arrays;

class System extends Controller
{
    //--------------------------------------------------------------------------------------------------------
    // Converter
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function converter(String $params = NULL)
    {
        if( Method::post('convert') )
        {
            $orm = Method::post('sql');

            $this->_cselect($orm);
            $this->_cdelete($orm);
        }

        $this->masterpage->pdata['supportQueries'] =
        [
            '<b>select</b> columns <b>from</b> table [<b>where</b> column cond value] [<b>limit</b> start, limit] [<b>group by</b> column] [<b>order by</b> column asc|desc]',
            '<b>delete</b> <b>from</b> table <b>where</b> column cond value'
        ];
        $this->masterpage->pdata['orm'] = str_replace('->', '<br>&nbsp;&nbsp;->', $orm ?? NULL) . ';';
        $this->masterpage->page  = 'converter';
    }

    //--------------------------------------------------------------------------------------------------------
    // Info
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function info(String $params = NULL)
    {
        $this->masterpage->page  = 'info';
    }

    //--------------------------------------------------------------------------------------------------------
    // Log
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
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

    //--------------------------------------------------------------------------------------------------------
    // Protected Convert Delete
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string &$replace
    //
    //--------------------------------------------------------------------------------------------------------
    protected function _cdelete(&$replace)
    {
        $delete  = '^delete\s+';

        if( preg_match('/' . $delete . '/', $replace))
        {
            $replace = suffix($replace, ';');

            $from    = 'from\s+(.*?)(\;|\s+)';
            $where   = 'where\s+(.*?)(\;|\s+)';
            $where2  = 'where\s+(.*?)\s+(.*?)\s+(.*?)(\;|\s+)';

            $data =
            [
                '/'.$delete.'/i'  => 'DB',
                '/'.$from.'/i'    => '->delete(\'$1\')',
                '/'.$where2.'/i'  => '::where(\'$1 $2\', \'$3\')',
                '/'.$where.'/i'   => '::where(\'exp:$1\')'
            ];

            $replace = preg_replace(Arrays::keys($data), Arrays::values($data), $replace);

            $this->_last($replace, '/\-\>delete\(.*?\)/');
        }
    }

    //--------------------------------------------------------------------------------------------------------
    // Protected Convert Select
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string &$replace
    //
    //--------------------------------------------------------------------------------------------------------
    protected function _cselect(&$replace)
    {
        $select  = '^select\s+(.*?)\s+';

        if( preg_match('/' . $select . '/', $replace))
        {
            $replace = suffix($replace, ';');

            $from    = 'from\s+(.*?)(\;|\s+)';
            $where   = 'where\s+(.*?)(\;|\s+)';
            $where2  = 'where\s+(.*?)\s+(.*?)\s+(.*?)(\;|\s+)';
            $limit   = 'limit\s+([0-9]+)(\;|\s+)';
            $limit2  = 'limit\s+([0-9]+\s*\,\s*[0-9]+)(\;|\s+)';
            $orderBy = 'order\s+by\s+(.*?)\s+(asc|desc)(\;|\s+)';
            $groupBy = 'group\s+by\s+(\w+)(\;|\s+)';

            $data =
            [
                '/'.$select.'/i'  => 'DB::select(\'$1\')',
                '/'.$from.'/i'    => '->get(\'$1\')',
                '/'.$where2.'/i'  => '->where(\'$1 $2\', \'$3\')',
                '/'.$where.'/i'   => '->where(\'exp:$1\')',
                '/'.$limit2.'/i'  => '->limit($1)',
                '/'.$limit.'/i'   => '->limit($1)',
                '/'.$orderBy.'/i' => '->orderBy(\'$1\', \'$2\')',
                '/'.$groupBy.'/i' => '->groupBy(\'$1\')',
            ];

            $replace = preg_replace(Arrays::keys($data), Arrays::values($data), $replace);

            $this->_last($replace, '/\-\>get\(.*?\)/');
        }
    }

    //--------------------------------------------------------------------------------------------------------
    // Protected Last
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string &$replace
    // @param string  $getRegex
    //
    //--------------------------------------------------------------------------------------------------------
    protected function _last(&$replace, $getRegex)
    {
        preg_match($getRegex, $replace, $match);

        $get = $match[0] ?? NULL;

        $replace = preg_replace($getRegex, '', $replace) . $get;
    }

}

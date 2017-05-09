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

use Method, Folder, File, Html, Arrays, Restful, Separator;

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
            $orm = trim(Method::post('sql'));

            $this->_cselect($orm);
            $this->_cdelete($orm);
            $this->_cinsert($orm);
            $this->_cupdate($orm);

            $orm = suffix(str_replace('->', '<br>&nbsp;&nbsp;->', $orm), ';');

            $this->masterpage->pdata['orm'] = $orm;
        }

        $this->masterpage->pdata['supportQueries'] =
        [
            '<b>select</b> columns <b>from</b> table [<b>where</b> column cond value] [<b>limit</b> start, limit] [<b>group by</b> column] [<b>order by</b> column asc|desc]',
            '<b>insert into</b> table (col1, col2, ...) <b>values</b>(val1, val2, ...)',
            '<b>update</b> table <b>set</b> column1 = value1 ... [<b>where</b> column cond value]',
            '<b>delete</b> <b>from</b> table <b>where</b> column cond value'
        ];

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
        $return = Restful::post('https://api.znframework.com/statistics/upgrade', ['version' => ZN_VERSION]);

        $return = Separator::decodeArray($return);

        if( ! empty($return) )
        {
            $this->masterpage->pdata['upgrades'] = Arrays::keys($return);

            if( Method::post('upgrade') )
            {
                foreach( $return as $file => $content )
                {
                    File::write($file, $content);
                }

                redirect(currentPath());
            }
        }

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
            $project = SELECT_PROJECT;

            $path = PROJECTS_DIR . $project . DS . 'Storage/Logs/';

            $files = Folder::files($path, 'log');

            $this->masterpage->pdata['files'] = $files;
            $this->masterpage->pdata['path']  = $path;
        }

        $this->masterpage->page  = 'logs';
    }

    //--------------------------------------------------------------------------------------------------------
    // Protected Convert Update
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string &$replace
    //
    //--------------------------------------------------------------------------------------------------------
    protected function _cupdate(&$replace)
    {
        $update = '^update\s+';

        if( preg_match('/' . $update . '/', $replace))
        {
            $replaceEx = explode('where', $replace);
            $whereClause = $replaceEx[1] ?? NULL;
            $replace   = suffix($replaceEx[0], ';');

            $syntax = '/'.$update.'(.*?)\s+set\s+(.*?)(\s+|\;)$/si';

            preg_match($syntax, $replace, $match);

            if( $whereClause )
            {
                $where = preg_replace('/(\w+)\s+(\W+)\s+(.*?)\;/si', 'where(\'$1 $2\', \'$3\')->', $whereClause.';');
            }

            $columns = explode(',', $match[2] ?? NULL);

            $options = '[';

            foreach( $columns as $val )
            {
                $valEx = explode('=', trim($val));

                $options .= presuffix(trim($valEx[0]), '\'') . ' => ' . trim($valEx[1]) . ', ';
            }

            $options = rtrim($options, ', ');
            $options .= ']';
            $replace = preg_replace($syntax, 'DB::' . trim($where) . 'update(\'$1\', '.$options.')', $replace);
        }
    }

    //--------------------------------------------------------------------------------------------------------
    // Protected Convert Insert
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string &$replace
    //
    //--------------------------------------------------------------------------------------------------------
    protected function _cinsert(&$replace)
    {
        $insert  = '^insert\s+';

        if( preg_match('/' . $insert . '/', $replace))
        {
            $replace = suffix($replace, ';');

            $syntax = '/'.$insert.'into\s+(.*?)\s*\((.*?)\)\s+values\s*\((.*?)\)/si';

            preg_match($syntax, $replace, $match);

            $columns = explode(',', $match[2] ?? NULL);
            $values  = explode(',', $match[3] ?? NULL);

            $options = '[';
            foreach( $columns as $key => $val )
            {
                $options .= presuffix(trim($val), '\'') . ' => ' . trim($values[$key]) . ', ';
            }
            $options = rtrim($options, ', ');
            $options .= ']';
            $replace = preg_replace($syntax, 'DB::insert(\'$1\', '.$options.')', $replace);
        }
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
                '/'.$delete.'/si'  => '',
                '/'.$from.'/si'    => '->delete(\'$1\')',
                '/'.$where2.'/si'  => '->where(\'$1 $2\', \'$3\')',
                '/'.$where.'/si'   => '->where(\'exp:$1\')'
            ];

            $replace = preg_replace(Arrays::keys($data), Arrays::values($data), $replace);

            $this->_last($replace, '/\-\>delete\(.*?\)/', 'DB');
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
                '/'.$select.'/si'  => '->select(\'$1\')',
                '/'.$from.'/si'    => '->get(\'$1\')',
                '/'.$where2.'/si'  => '->where(\'$1 $2\', \'$3\')',
                '/'.$where.'/si'   => '->where(\'exp:$1\')',
                '/'.$limit2.'/si'  => '->limit($1)',
                '/'.$limit.'/si'   => '->limit($1)',
                '/'.$orderBy.'/si' => '->orderBy(\'$1\', \'$2\')',
                '/'.$groupBy.'/si' => '->groupBy(\'$1\')',
            ];

            $replace = preg_replace(Arrays::keys($data), Arrays::values($data), $replace);

            $replace = str_replace('->select(\'*\')', '', $replace);

            $this->_last($replace, '/\-\>get\(.*?\)/', 'DB');
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
    protected function _last(&$replace, $getRegex, $class)
    {
        preg_match($getRegex, $replace, $match);

        $get = $match[0] ?? NULL;

        $replace = $class . preg_replace($getRegex, '', $replace) . $get;

        $replace = str_replace('DB->', 'DB::', $replace);
    }

}

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

use Method, Folder, File, Html, Arrays, Restful, Separator, Http, Session, DBTool, DB, Form, DBGrid;

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
            $this->_ccreateTable($orm);
            $this->_cdropTable($orm);
            $this->_ccreateDatabase($orm);
            $this->_cdropDatabase($orm);

            $orm = suffix(str_replace('->', '<br>&nbsp;&nbsp;->', $orm), ';');

            $this->masterpage->pdata['orm'] = $orm;
        }

        $this->masterpage->pdata['supportQueries'] =
        [
            '<b>select</b> columns <b>from</b> table_name [<b>where</b> column cond value] [<b>limit</b> start, limit] [<b>group by</b> column] [<b>order by</b> column asc|desc]',
            '<b>insert into</b> table_name (col1, col2, ...) <b>values</b>(val1, val2, ...)',
            '<b>update</b> table_name <b>set</b> column1 = value1 ... [<b>where</b> column cond value]',
            '<b>delete</b> <b>from</b> table_name <b>where</b> column cond value',
            '<b>create</b> <b>table</b> table_name (columns ... values)',
            '<b>drop</b> <b>table</b> table_name',
            '<b>create</b> <b>database</b> database_name',
            '<b>drop</b> <b>database</b> database_name'
        ];

        $this->masterpage->page  = 'converter';
    }

    //--------------------------------------------------------------------------------------------------------
    // Converter
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function language(String $params = NULL)
    {
        $this->masterpage->pdata['table']  = \MLS::limit(DASHBOARD_CONFIG['limits']['language'])->create();

        $this->masterpage->page  = 'language';
    }

    //--------------------------------------------------------------------------------------------------------
    // Converter
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function grid(String $params = NULL)
    {
        $tables             = DBTool::listTables();
        $tables['none']     = 'none';
        $sessionSelectTable = Session::select('gridSelectTable');
        $joinColumns        = Session::select('gridJoinColumns');
        $columns            = Session::select('gridColumns');
        $postColumn         = Session::select('gridColumn');
        $selectTable        = ! empty($sessionSelectTable ) ? $sessionSelectTable : $tables[0];

        $this->masterpage->pdata['tables'] = Arrays::combine($tables, $tables);

        if( Method::post('show') )
        {
            Session::delete('gridJoinColumns');
            Session::delete('gridColumns');
            Session::delete('gridColumn');

            $joinColumns    = [];
            $columns        = [];
            $selectTable    = Method::post('table');
            $postColumn     = Method::post('column');
            $joinMainColumn = Method::post('joinMainColumn');
            $joinMainTable  = Method::post('joinMainTable');
            $joinTypes      = Method::post('joinTypes');

            Session::insert('gridSelectTable', $selectTable);

            if( ! empty($joinMainColumn) )
            {
                if( count($joinMainColumn) > count($joinTypes) )
                {
                    $joinMainColumn = Arrays::removeFirst($joinMainColumn);
                }

                foreach( $joinMainColumn as $key => $column )
                {
                    if( $joinMainTable[$key] !== 'none' && ! empty($column) )
                    {
                        $columns       = array_merge($columns, DB::get($joinMainTable[$key])->columns());
                        $joinColumns[] = [$joinMainTable[$key].'.'.$column, $selectTable . '.' . $postColumn, $joinTypes[$key]];
                    }
                }
            }
        }

        $columns = DB::get($selectTable)->columns();

        DBGrid::limit(DASHBOARD_CONFIG['limits']['grid']);

        if( ! empty($joinColumns) )
        {
            Session::insert('gridJoinColumns', $joinColumns);
            Session::insert('gridColumns'    , $columns);
            Session::insert('gridColumn'     , $postColumn);

            DBGrid::joins(...$joinColumns);
        }

        DBGrid::search(...$columns);

        $this->masterpage->pdata['table']       = DBGrid::create($selectTable);
        $this->masterpage->pdata['selectTable'] = $selectTable;
        $this->masterpage->pdata['column']      = $postColumn;
        $this->masterpage->pdata['columns']     = Arrays::combine($columns, $columns);
        $this->masterpage->page                 = 'grid';
    }

    public function gridSelectJoinTableAjax()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $table = Method::post('table');
        $type  = Method::post('type');

        if( $table === 'none' )
        {
            return false;
        }

        $columns = DB::get($table)->columns();

        $str  = Form::class('form-control')->select($type === 'sub' ? 'joinMainColumn[]' : 'column', Arrays::combine($columns, $columns));
        $str .= $type === 'sub' ? Form::class('form-control')->select('joinTypes[]', ['left' => 'Left', 'right' => 'Right', 'inner' => 'Inner']) : NULL;

        echo $str;
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

        if( Method::post('upgrade') )
        {
            if( ! empty($return) )
            {
                foreach( $return as $file => $content )
                {
                    Folder::create(pathInfos($file, 'dirname'));
                    File::write($file, $content);
                }

                redirect(currentUri(), 0, ['success' => LANG['success']]);
            }
            else
            {
                $this->masterpage->error = LANG['alreadyVersion'];
            }
        }

        $this->masterpage->pdata['upgrades'] = Arrays::keys($return);
        $this->masterpage->page              = 'info';
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
        $project = SELECT_PROJECT;
        $path    = PROJECTS_DIR . $project . DS . 'Storage/Logs/';
        $files   = Folder::files($path, 'log');

        if( empty($files) )
        {
            $this->masterpage->error = LANG['notFound'];
        }

        $this->masterpage->pdata['files'] = $files;
        $this->masterpage->pdata['path']  = $path;
        $this->masterpage->page           = 'logs';
    }

    //--------------------------------------------------------------------------------------------------------
    // Protected Clear Command
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    protected function clearCommand()
    {
        unset($_SESSION['persistCommands']);
        unset($_SESSION['commandResponses']);
        unset($_SESSION['commands']);
    }

    //--------------------------------------------------------------------------------------------------------
    // Terminal
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function terminal(String $params = NULL)
    {
        if( IS_CONTAINER )
        {
            redirect();
        }

        $this->masterpage->pdata['supportCommands'] =
        [
            '<b>run-uri</b> controller/function/p1/p2 ... /pN',
            '<b>run-controller</b> controller/function',
            '<b>run-class</b> controller:function p1 p2 ... pN',
            '<b>run-model</b> model:function p1 p2 p3 ... pN'   ,
            '<b>run-function</b> function p1 p2 p 3 ... pN ',
            '<b>run-command</b> command:method p1 p2 p 3 ... pN ',
            '<b>run-external-command</b> command:method p1 p2 p 3 ... pN ',
            '<b>clear</b>'
        ];

        $this->masterpage->page  = 'terminal';
    }

    //--------------------------------------------------------------------------------------------------------
    // Terminal Ajax
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function terminalAjax()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $command          = Method::post('command');
        $previousCommands = NULL;

        if( $command === 'clear' )
        {
            Session::delete('commands');
            echo ''; exit;
        }

        if( $getCommands = Session::select('commands') )
        {
            Session::insert('commands', Arrays::addLast($getCommands, $command));
        }
        else
        {
            Session::insert('commands', [$command]);
        }

        exec('php zerocore project-name ' . SELECT_PROJECT. ' '.$command.' 2>&1', $response);

        $string = NULL;

        foreach( $response as $val )
        {
            $string .= $val . EOL;
        }

        echo $string;
    }

    //--------------------------------------------------------------------------------------------------------
    // Terminal Ajax
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function terminalArrowAjax()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $index    = Method::post('index');
        $commands = Session::select('commands');

        if( ! empty($commands[$index]) )
        {
            echo $commands[$index]; exit;
        }

        echo NULL;
    }

    //--------------------------------------------------------------------------------------------------------
    // Backup
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function backup(String $params = NULL)
    {
        $project = SELECT_PROJECT;
        $path    = STORAGE_DIR . 'ProjectBackup' . DS;

        if( ! Folder::exists($path) )
        {
            Folder::create($path);
        }

        if( Method::post('backup') )
        {
            $fix      = '-' .\Date::convert(\Date::current() . \Time::current(), 'Y-m-d-H-i-s');
            $project  = $project . $fix;
            $fullPath = $path . $project;

            $databaseConfigPath = SELECT_PROJECT_DIR . 'Config' . DS . 'Database.php';

            if( Method::post('databaseBackup') )
            {
                \DBTool::backup('*', 'db.sql', $fullPath);
            }

            Folder::copy(SELECT_PROJECT_DIR, $fullPath);

            redirect(currentUri(), 0, ['success' => LANG['success']]);
        }

        $files = Folder::files($path, 'dir');

        if( empty($files) )
        {
            $this->masterpage->error = LANG['notFound'];
        }

        $this->masterpage->pdata['files'] = $files;
        $this->masterpage->pdata['path']  = $path;
        $this->masterpage->page           = 'backup';
    }

    //--------------------------------------------------------------------------------------------------------
    // Protected Convert Create Database
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string &$replace
    //
    //--------------------------------------------------------------------------------------------------------
    protected function _ccreateDatabase(&$replace)
    {
        $query  = '^create\s+database\s+';

        if( preg_match('/' . $query . '/i', $replace))
        {
            $replace = suffix($replace, ';');
            $syntax  = '/'.$query.'(\w+)/si';
            $replace = preg_replace($syntax, 'DBForge::createDatabase(\'$1\')', $replace);
        }
    }

    //--------------------------------------------------------------------------------------------------------
    // Protected Convert Drop Database
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string &$replace
    //
    //--------------------------------------------------------------------------------------------------------
    protected function _cdropDatabase(&$replace)
    {
        $query  = '^drop\s+database\s+';

        if( preg_match('/' . $query . '/i', $replace))
        {
            $replace = suffix($replace, ';');
            $syntax  = '/'.$query.'(\w+)/si';
            $replace = preg_replace($syntax, 'DBForge::dropDatabase(\'$1\')', $replace);
        }
    }

    //--------------------------------------------------------------------------------------------------------
    // Protected Convert Drop Table
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string &$replace
    //
    //--------------------------------------------------------------------------------------------------------
    protected function _cdropTable(&$replace)
    {
        $query  = '^drop\s+table\s+';

        if( preg_match('/' . $query . '/i', $replace))
        {
            $replace = suffix($replace, ';');
            $syntax  = '/'.$query.'(\w+)/si';
            $replace = preg_replace($syntax, 'DBForge::dropTable(\'$1\')', $replace);
        }
    }

    //--------------------------------------------------------------------------------------------------------
    // Protected Convert Create Table
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string &$replace
    //
    //--------------------------------------------------------------------------------------------------------
    protected function _ccreateTable(&$replace)
    {
        $query  = '^create\s+table\s+';

        if( preg_match('/' . $query . '/i', $replace))
        {
            $replace = suffix($replace, ';');
            $syntax  = '/'.$query.'(.*?)\s*\((.*)\)/si';

            preg_match($syntax, $replace, $match);

            $columns = explode(',', $match[2] ?? NULL);
            $options = '[';

            foreach( $columns as $val )
            {
                $val = trim($val);

                $valEx  = explode(' ', $val);
                $column = $valEx[0] ?? NULL;

                $options .= presuffix(trim($column), '\'') . ' => ' . presuffix(trim(str_replace($column, '', $val)), '\'') . ', ';
            }

            $options = rtrim($options, ', ');
            $options .= ']';
            $replace = preg_replace($syntax, 'DBForge::createTable(\'$1\', '.$options.')', $replace);
        }
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

        if( preg_match('/' . $update . '/i', $replace))
        {
            $replaceEx   = explode('where', $replace);
            $whereClause = $replaceEx[1] ?? NULL;
            $replace     = suffix($replaceEx[0], ';');
            $syntax      = '/'.$update.'(.*?)\s+set\s+(.*?)(\s+|\;)$/si';

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

            $options  = rtrim($options, ', ');
            $options .= ']';
            $replace  = preg_replace($syntax, 'DB::' . trim($where) . 'update(\'$1\', '.$options.')', $replace);
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
        $insert = '^insert\s+';

        if( preg_match('/' . $insert . '/i', $replace))
        {
            $replace = suffix($replace, ';');
            $syntax  = '/'.$insert.'into\s+(.*?)\s*\((.*?)\)\s+values\s*\((.*?)\)/si';

            preg_match($syntax, $replace, $match);

            $columns = explode(',', $match[2] ?? NULL);
            $values  = explode(',', $match[3] ?? NULL);
            $options = '[';

            foreach( $columns as $key => $val )
            {
                $options .= presuffix(trim($val), '\'') . ' => ' . trim($values[$key]) . ', ';
            }

            $options  = rtrim($options, ', ');
            $options .= ']';
            $replace  = preg_replace($syntax, 'DB::insert(\'$1\', '.$options.')', $replace);
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

        if( preg_match('/' . $delete . '/i', $replace))
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

        if( preg_match('/' . $select . '/i', $replace))
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

        $get     = $match[0] ?? NULL;
        $replace = $class . preg_replace($getRegex, '', $replace) . $get;
        $replace = str_replace('DB->', 'DB::', $replace);
    }
}

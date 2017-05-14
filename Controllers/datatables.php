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

use Http, Method, DBForge, DB, Import, DBTool, Session, Config, Security, Arrays, Json, Folder, File;

class Datatables extends Controller
{
    //--------------------------------------------------------------------------------------------------------
    // Controller
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function main(String $params = NULL)
    {
        $this->masterpage->plugin['name'] = array_merge
        (
            Config::get('Masterpage', 'plugin')['name'],
            [
                'Dashboard/highlight/styles/agate.css',
                'Dashboard/highlight/highlight.pack.js'
            ]
        );

        $this->masterpage->pdata['tables'] = DBTool::listTables();

        $this->masterpage->page = 'datatables';
    }

    //--------------------------------------------------------------------------------------------------------
    // Save File
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function createTable()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $content = Method::post('content');
        $content = Security::htmlDecode($content);
        $return  = eval('?>' . $content);

        $table   = Arrays::key($return);
        $columns = Arrays::value($return);
        $status  = DBForge::createTable($table, $columns);

        if( $status )
        {
            $projectTablesDir = STORAGE_DIR . 'ProjectTables' . DS . CURRENT_DATABASE . DS;

            if( ! Folder::exists($projectTablesDir) )
            {
                Folder::create($projectTablesDir);
            }

            File::write($projectTablesDir . suffix($table, '.php'), $content);
        }

        $result  = Import::usable()->view('datatables-tables.wizard', ['tables' => DBTool::listTables()]);

        echo Json::encode
        ([
            'status' => $status,
            'result' => $result,
            'error'  => DBForge::error()
        ]);
    }

    //--------------------------------------------------------------------------------------------------------
    // Save File
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $params NULL
    //
    //--------------------------------------------------------------------------------------------------------
    public function alterTable()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $content = Method::post('content');
        $content = Security::htmlDecode($content);
        $return  = eval('?>' . $content);
        $type    = Method::post('type');
        $table   = Method::post('table');
        $newName = Arrays::key($return);
        $columns = Arrays::value($return);
        $status  = false;

        $projectTablesDir = STORAGE_DIR . 'ProjectTables' . DS . CURRENT_DATABASE . DS;

        if( ! Folder::exists($projectTablesDir) )
        {
            Folder::create($projectTablesDir);
        }

        if( $type === 'renameTable' )
        {
            if( $status = DBForge::alterTable($table, [$type => $newName]) )
            {
                File::delete($projectTablesDir . suffix($table, '.php'), $content);
                File::write($projectTablesDir . suffix($newName, '.php'), $content);
            }
        }
        elseif( $type === 'addColumn' )
        {
            foreach( $columns as $key => $column )
            {
                $status = DBForge::alterTable($table, [$type => [$key => $column]]);
            }

            if( $status )
            {
                File::write($projectTablesDir . suffix($table, '.php'), $content);
            }
        }
        elseif( $type === 'modifyColumn' )
        {
            if( $status = DBForge::alterTable($table, [$type => $columns]) )
            {
                File::write($projectTablesDir . suffix($table, '.php'), $content);
            }
        }
        elseif( $type === 'renameColumn' )
        {
            foreach( $columns as $key => $column )
            {
                $status = DBForge::alterTable($table, [$type => [$key => $column]]);
            }

            if( $status )
            {
                File::write($projectTablesDir . suffix($table, '.php'), $content);
            }
        }
        elseif( $type === 'dropTable' )
        {
            if( $status = DBForge::dropTable($table) )
            {
                File::delete($projectTablesDir . suffix($table, '.php'), $content);
            }
        }
        else if( $type === 'dropColumn' )
        {
            $status = DBForge::alterTable($table, [$type => $columns]);
        }

        $result  = Import::usable()->view('datatables-tables.wizard', ['tables' => DBTool::listTables()]);

        echo Json::encode
        ([
            'status' => $status,
            'result' => $result,
            'error'  => DBForge::error()
        ]);
    }

    public function dropTable()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $table   = Method::post('table');
        $status  = DBForge::dropTable($table);
        $result  = Import::usable()->view('datatables-tables.wizard', ['tables' => DBTool::listTables()]);

        echo Json::encode
        ([
            'status' => $status,
            'result' => $result,
            'error'  => DBForge::error()
        ]);
    }

    public function updateRows()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $post = Method::post();

        $columns = $post['columns'];
        $table   = $post['table'];
        $uniqueKey = $post['uniqueKey'];
        $newData = [];

        $i = 0;


        foreach( $columns as $key => $values )
        {
            foreach( $values as $value )
            {
                $newData[$i][$key] = $value;

                DB::where($uniqueKey, $newData[$i][$uniqueKey])->update($table, $newData[$i]);

                $i++;
            }

            $i = 0;
        }

        Import::view('datatables-rows.wizard', ['table' => $table, 'start' => (int) Session::select($table . 'paginationStart')]);
    }

    public function addRow()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $post = Method::post();

        $columns = $post['addColumns'];
        $table   = $post['table'];
        $newData = [];

        $i = 0;

        foreach( $columns as $key => $values )
        {
            foreach( $values as $value )
            {
                $newData[$i][$key] = $value;

                DB::insert($table, $newData[$i]);

                $i++;
            }

            $i = 0;
        }

        Import::view('datatables-rows.wizard', ['table' => $table, 'start' => (int) Session::select($table . 'paginationStart')]);
    }

    public function deleteRow()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $table  = Method::post('table');
        $column = Method::post('column');
        $value  = Method::post('value');

        DB::where($column, $value)->delete($table);

        Import::view('datatables-rows.wizard', ['table' => $table, 'start' => (int) Session::select($table . 'paginationStart')]);
    }

    public function paginationRow()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $table = Method::post('table');
        $start = Method::post('start');

        Session::insert($table . 'paginationStart', $start);

        Import::view('datatables-rows.wizard', ['table' => $table, 'start' => $start]);
    }
}

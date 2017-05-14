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
    public function alterTable()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $content = Security::htmlDecode(Method::post('content'));
        $page    = Method::post('page');
        $status  = eval('?><?php ' . suffix($content, ';'));
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

        DB::insert('ignore:' . $table, $columns);

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

    public function updateRow()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $table   = Method::post('table');
        $column  = Method::post('uniqueKey');
        $ids     = Method::post('ids');
        $columns = $_POST['updateColumns'][$ids]; // Origin Data

        DB::where($column, $ids)->update('ignore:' . $table, $columns);

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

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

use Http, Method, DBForge, DB, Import, DBTool, Session, Config, Security, Arrays, Json;

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

        Import::view('datatables-rows.wizard', ['table' => $table, 'start' => (int) Session::select('paginationStart')]);
    }

    public function paginationRow()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $table = Method::post('table');
        $start = Method::post('start');

        Session::insert('paginationStart', $start);

        Import::view('datatables-rows.wizard', ['table' => $table, 'start' => $start]);
    }
}

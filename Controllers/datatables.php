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

use Http, Method, DBForge, DB, Import, DBTool, Session;

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
        $this->masterpage->pdata['tables'] = DBTool::listTables();

        $this->masterpage->page = 'datatables';
    }

    public function dropTable()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $table = Method::post('table');

        DBForge::dropTable($table);

        Import::view('datatables-tables.wizard', ['tables' => DBTool::listTables()]);
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

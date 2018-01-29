<?php namespace Project\Controllers;

use DB;
use Http;
use Post;
use Import;

class Experiments extends Controller
{
    /**
     * Main
     */
    public function main(String $params = NULL)
    {
        Masterpage::page('experiment');
    }

    /**
     * Ajax Alter Table
     */
    public function alterTable()
    {
        if( ! Http::isAjax() )
        {
            return false;
        }

        $content = Post::content();
        $type    = Post::type();

        if( $type === 'php' )
        {
            eval('?>' . html_entity_decode($content, ENT_QUOTES)); exit;
        }
        else
        {
            $query = DB::query($content);

            $result = Import::view('experiments-table', ['columns' => $query->columns(), 'result' => $query->resultArray()], true);
        }

        echo $result ?: LANG['noOutput'];
    }
}
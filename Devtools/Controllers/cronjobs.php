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

use Post, Crontab;

class Cronjobs extends Controller
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
        {
            redirect();
        }

        if( Post::create() )
        {
            $method = Post::type();
            $metval = Post::typeval();
            $status = false;

            if( ($time = Post::certain()) !== 'none' )
            {
                $status = Crontab::$time();
            }
            elseif( ($time = Post::per()) !== 'none' )
            {
                $status = Crontab::$time(Post::perval());
            }
            else
            {
                if( ($time = Post::minute()) !== 'none' )
                {
                    $status = Crontab::$time(Post::minuteval());
                }

                if( ($time = Post::hour()) !== 'none' )
                {
                    $status = Crontab::$time(Post::hourval());
                }

                if( ($time = Post::day()) !== 'none' )
                {
                    $status = Crontab::$time(Post::dayval());
                }

                if( ($time = Post::month()) !== 'none' )
                {
                    $status = Crontab::$time(Post::monthval());
                }
            }

            if( $status === false )
            {
                return $this->masterpage->error = LANG['crontabTimeError'];
            }
            else
            {
                Crontab::$method($metval);
            }
        }

        if( Crontab::list() )
        {
            $list = Crontab::listArray();
        }

        $this->masterpage->pdata['list'] = $list ?? [];

        $this->masterpage->page = 'cronjob';
    }

    //--------------------------------------------------------------------------------------------------------
    // Delete
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function delete(Int $id)
    {
        Crontab::remove($id);

        redirect('cronjobs');
    }
}

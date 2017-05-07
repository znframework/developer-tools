<?php namespace Project\Controllers;


class Info extends Controller
{
    public function main(String $params = NULL)
    {
        $this->masterpage->page  = 'info';
    }
}

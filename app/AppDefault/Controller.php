<?php


namespace AppDefault;


class Controller
{
    public function __invoke(\Aerys\Request $req, \Aerys\Response $res, $routeArgs = [])
    {
        $res->end("<html><body><h1>AppDefault/Controller</h1></body></html>");
    }
}
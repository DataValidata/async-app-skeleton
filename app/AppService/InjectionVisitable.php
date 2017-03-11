<?php
/**
 * Created by PhpStorm.
 * User: ciaran
 * Date: 11/03/17
 * Time: 01:20
 */

namespace AppService;


use Auryn\Injector;

interface InjectionVisitable
{
    public static function receiveInjectionVisit(Injector $injector);
}
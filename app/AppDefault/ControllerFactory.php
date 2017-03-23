<?php
/**
 * Created by PhpStorm.
 * User: ciaran
 * Date: 23/03/17
 * Time: 01:12
 */

namespace AppDefault;


class ControllerFactory
{
    /** @var \DateTime  */
    private $dateTime;
    private $offset;

    public function __construct(\DateTime $dateTime, $offset)
    {
        $this->dateTime = $dateTime;
        $this->offset = $offset;
    }

    function __invoke()
    {
        return new Controller($this->dateTime, $this->offset);
    }
}
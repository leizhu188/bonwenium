<?php
require __DIR__.'/../vendor/autoload.php';

//function autoload()
//{
////    include_once \Kernel\TestB::class;
////    include_once \Kernel\TestA::class;
//}
//spl_autoload_register('autoload', true, true);

$container = new \Kernel\Container();

$testB = $container->get(\Kernel\TestB::class);

$testB->echo();


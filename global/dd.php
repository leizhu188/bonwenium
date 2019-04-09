<?php
use \Symfony\Component\VarDumper\Dumper\CliDumper;
use \Symfony\Component\VarDumper\Dumper\HtmlDumper;
use \Symfony\Component\VarDumper\Cloner\VarCloner;
/**
 * @author bonwe
 * 自定义全局方法
 */
if (! function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {
        array_map(function ($x) {
            if (class_exists(CliDumper::class)) {
                $dumper = 'cli' === PHP_SAPI ? new CliDumper : new HtmlDumper();
                $dumper->dump((new VarCloner())->cloneVar($x));
            } else {
                var_dump($x);
            }
        }, func_get_args());

        die(1);
    }
}
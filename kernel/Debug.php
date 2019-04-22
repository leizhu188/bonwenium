<?php

namespace Kernel;

class Debug extends  Controller
{

    /**
     */
    public function handle($stepName)
    {
        $steps = [];
        foreach (debug($stepName) as $module){
            $steps []= $module;
        }

        self::handleSteps($steps);
    }
}

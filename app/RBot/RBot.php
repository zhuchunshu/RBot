<?php

namespace App\RBot;

use App\RBot\Annotation\RBotInit;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;


#[Controller]
#[RBotInit]
class RBot
{
    public string|object|array|null $msg;

    public function handle($msg)
    {
        $RBotClass = AnnotationCollector::getClassesByAnnotation(RBotInit::class);
        $this->initPlugins($RBotClass,$msg);
    }

    public function initPlugins($RBotClass,$msg)
    {
        foreach ($RBotClass as $key=>$value){
            $Plugin = explode('\\', $key);
            if(count($Plugin)>=3 && $Plugin[1]==="Plugins"){
                $Plugin = $Plugin[2];
            }
            if(@is_dir(BASE_PATH . "/app/Plugins/" . $Plugin) && in_array($Plugin, Plugins_EnList(), true) && method_exists(new $key(),"handle")) {
                (new $key())->handle($msg);
            }
        }
    }


}
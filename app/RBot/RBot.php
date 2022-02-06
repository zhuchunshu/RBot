<?php

namespace App\RBot;

use App\RBot\Annotation\RBotInit;
use App\RBot\Annotation\RBotOnMessage;
use Hyperf\Di\Annotation\AnnotationCollector;


#[RBotInit]
class RBot
{
    public string|object|array|null $msg;

    public function handle($msg)
    {
        $RBotClass = AnnotationCollector::getClassesByAnnotation(RBotInit::class);
        $RBotOnMessage = AnnotationCollector::getMethodsByAnnotation(RBotOnMessage::class);
        $this->initPlugins($RBotClass,$msg);
        $this->initOnMessage($RBotOnMessage,$msg);
    }

    public function initPlugins($RBotClass,$msg)
    {
        foreach ($RBotClass as $key=>$value){
            $Plugin = explode('\\', $key);
            if(count($Plugin)>=3 && $Plugin[1]==="Plugins"){
                $Plugin = $Plugin[2];
            }
            if(@is_dir(BASE_PATH . "/app/Plugins/" . $Plugin) && in_array($Plugin, Plugins_EnList(), true) && method_exists(new $key(),"handle")) {
                (new $key())->handle($msg,RBotMsg($msg));
            }
        }
    }

    public function initOnMessage($RBotOnMessage,$msg): void
    {
        //$RBotOnMessage = AnnotationCollector::getMethodsByAnnotation(RBotOnMessage::class);
        $data = RBotMsg($msg);
        foreach ($RBotOnMessage as $value){
            $Plugin = explode('\\', $value['class']);
            if(count($Plugin)>=3 && $Plugin[1]==="Plugins"){
                $Plugin = $Plugin[2];
            }
            if(is_dir(BASE_PATH . "/app/Plugins/" . $Plugin) && in_array($Plugin, Plugins_EnList(), true)) {
                // 验证annotation
                $annotation = $value['annotation'];
                $run = false;
                if($data->type()===$annotation->post_type){
                    $run = true;
                }

                foreach ((array)$annotation as $annotation_key=>$annotation_value){
                    if(($annotation_key !== "post_type") && $annotation_value !== null && $annotation_value !== $data->$annotation_key()) {
                        $run = false;
                    }
                }
                if($run===true){
                    $class = $value['class'];
                    $method = $value['method'];
                    (new $class())->$method($msg,RBotMsg($msg));
                }
            }
        }
    }

}
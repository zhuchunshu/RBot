<?php

namespace App\Plugins\XueXi\src\Controller;

use App\RBot\Annotation\RBotInit;
use App\RBot\RBotMsg;

#[RBotInit]
class XueXi
{
    public RBotMsg $data;

    public function handle($msg) {

        $this->data = RBotMsg($msg);
        if($this->data->type()!=="message"){
            return ;
        }
        if($this->data->message_type()!=="private"){
            return ;
        }
        if($this->data->message()!=="强国好了"){
            return;
        }
        if(cache()->has("xuexi.qq.".$this->data->user_id())){
            sendMsg([
                "user_id" => $this->data->user_id(),
                "message" => "好了好了,今天先不学了....."
            ],"send_private_msg");
            return;
        }
        http_client()->get("http://127.0.0.1:9980/api/learn");
        sendMsg([
            "user_id" => $this->data->user_id(),
            "message" => "已开始学习"
        ],"send_private_msg");
        cache()->set("xuexi.qq.".$this->data->user_id(),time(),43200);
    }
}
<?php

namespace App\Plugins\XueXi\src\Controller;

use App\RBot\Annotation\RBotInit;
use App\RBot\RBotMsg;

#[RBotInit]
class QiangGuo
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
        if($this->data->message()!=="强国"){
            return;
        }
        $this->qiangguo();
    }

    public function qiangguo() {
        // 创建二维码
        go(function(){
            http_client()->get("http://127.0.0.1:9980/api/add");
        });
        // 获取二维码
        sendMsg([
            "user_id" => $this->data->user_id(),
            "message" => "已发送获取二维码请求..."
        ],"send_private_msg");
        // 发送二维码
        $this->qrcode();

    }

    // 获取二维码
    public function qrcode(){
        $list = http_client()->get("http://127.0.0.1:9980/api/list_qrurls");
        if(count($list['data'])===0){
            $this->qrcode();
        }
        foreach ($list['data'] as $value){
            $img = base64_image_content($value['url'],public_path("plugins/xuexi"));
            sendMsg([
                "user_id" => $this->data->user_id(),
                "message" => "[CQ:image,file=http://127.0.0.1:9501/plugins/xuexi/".$img."]\n\n请打开学习强国扫码登陆,扫码登录后发送:强国好了"
            ],"send_private_msg");
            \Swoole\Coroutine\System::exec("rm -rf ".public_path("plugins/xuexi/".$img));
            return;
        }
    }
}
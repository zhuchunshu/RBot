<?php

namespace App\RBot\Server\Http;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Utils\Context;
use Hyperf\Utils\Str;

#[Controller]
class ApiController
{
    #[PostMapping(path:"/admin/api/logger")]
    public function logger(){
        if(!admin_auth()->check()){
            return Json_Api(401,false,['无权限']);
        }
        $filename = date("Y-m-d").".log";
        $data = @file_get_contents(BASE_PATH."/app/RBot/Core/logs/".$filename);
        return Json_Api(200,true,$data);
    }

    #[PostMapping(path:"/admin/api/messages")]
    public function messages(){
        if(!admin_auth()->check()){
            return Json_Api(401,false,['无权限']);
        }
        $data = cache()->get("RBotMessage.".date("Ymd"));
        return Json_Api(200,true,(string)$data);
    }

    #[PostMapping(path:"/admin/api/getQrcode")]
    public function get_qrcode(){
        if(!admin_auth()->check()){
            return Json_Api(401,false,['无权限']);
        }
        // 有权限
        if(file_exists(public_path("qrcode.png"))){
            // 如果图片存在
            $token = Str::random();
            cache()->set("admin.qrcode.token",$token);
            return Json_Api(200,true,['url' => '/admin/assets/qrcode/'.$token.'.png']);
        }
        return Json_Api(403,false,['msg' => '无二维码']);
    }

    #[GetMapping(path:"/admin/assets/qrcode/{token}.png")]
    public function qrcode($token){
        if($token===cache()->get("admin.qrcode.token")){
            $img = file_get_contents(public_path("qrcode.png"));
            return ResponseObj()->withBody(SwooleStream($img))->withHeader("content-type","image/png; charset=utf-8");
        }
        return 'null';
    }
}
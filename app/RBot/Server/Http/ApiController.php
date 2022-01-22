<?php

namespace App\RBot\Server\Http;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Utils\Context;

#[Controller]
class ApiController
{
    #[PostMapping(path:"/admin/api/logger")]
    public function logger(){
        $filename = date("Y-m-d").".log";
        $data = @file_get_contents(BASE_PATH."/app/RBot/Core/logs/".$filename);
        return Json_Api(200,true,$data);
    }
}
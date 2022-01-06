<?php

use DivineOmega\PHPSummary\SummaryTool;
use JetBrains\PhpStorm\Pure;
use App\Plugins\Core\src\Lib\Redirect;

if(!function_exists("plugins_core_common_theme")){
    function plugins_core_common_theme($default="light"){
        if(!\App\Model\AdminOption::query()->where("name","theme_common_theme")->count()){
            return $default;
        }
        return \App\Model\AdminOption::query()->where("name","theme_common_theme")->first()->value;
    }
}

if(!function_exists("plugins_core_theme")){
    function plugins_core_theme(){
        if(session()->has("core.common.theme")){
            return session()->get("core.common.theme");
        }
        return plugins_core_common_theme();
    }
}


if(!function_exists("super_avatar")){
    function super_avatar($user_data): string
    {
        if($user_data->avatar){
            return $user_data->avatar;
        }

        if(get_options("core_user_def_avatar","gavatar")!=="multiavatar") {
            return get_options("theme_common_gavatar", "https://cn.gravatar.com/avatar/") . md5($user_data->email);
        }
        return "/user/multiavatar/".$user_data->username."/avatar.jpg";
    }
}


if(!function_exists("redirect")){
    #[Pure] function redirect(): Redirect
    {
        return new Redirect();
    }
}


if(!function_exists("Core_Ui")){
    function Core_Ui(): \App\Plugins\Core\src\Lib\Ui
    {
        return new App\Plugins\Core\src\Lib\Ui();
    }
}

if(!function_exists("core_Str_menu_url")){
    function core_Str_menu_url(string $path): string
    {
        if($path ==="//"){
            $path = "/";
        }
        return $path;
    }
}

if (!function_exists("core_menu_pd")) {
    function core_menu_pd(string $id)
    {
        foreach (Itf()->get('menu') as $value) {
            if (arr_has($value, "parent_id") && "menu_" . $value['parent_id'] === (string)$id) {
                return true;
            }
        }
        return false;
    }
}

if(!function_exists("core_Itf_id")){
    function core_Itf_id($name,$id){
        return \Hyperf\Utils\Str::after($id,$name."_");
    }
}

if (!function_exists("core_menu_pdArr")) {
    function core_menu_pdArr($id): array
    {
        $arr = [];
        foreach (Itf()->get("menu") as $key => $value) {
            if (arr_has($value, "parent_id") && "menu_".$value['parent_id'] === $id) {
                $arr[$key] = $value;
            }
        }
        return $arr;
    }
}

if(!function_exists("core_default")){
    function core_default($string=null,$default=null){
        if($string){
            return $string;
        }
        return $default;
    }
}

if(!function_exists("markdown")){
    function markdown(): \Parsedown
    {
        return new Parsedown();
    }
}

if(!function_exists("format_date")){
    function format_date($time)
    {
        $t = time() - strtotime($time);
        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒'
        );
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                return $c . $v . '前';
            }
        }
    }
}



if(!function_exists("core_http_build_query")){
    function core_http_build_query(array $data,array $merge): string
    {
        $data = array_merge($data,$merge);
        return http_build_query($data);
    }
}

if(!function_exists("core_http_url")){
    function core_http_url(): string
    {
        $query = http_build_query(request()->all());
        return request()->path()."?".$query;
    }
}

if(!function_exists("core_get_page")){
    function core_get_page(string $url): array
    {
        $data = explode("=",parse_url($url)['query']);
        return [$data[0] => $data[1]];
    }
}


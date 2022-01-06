<?php
return [
    "app" => [
        "name" => env("CodeFec_App_Name", "CodeFec"),
        "csrf" => (bool)env("CodeFec_App_Csrf", true)
    ],
    "RBot_pid_file" => BASE_PATH."/runtime/hyperf.pid"
];

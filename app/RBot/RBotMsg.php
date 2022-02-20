<?php

namespace App\RBot;


class RBotMsg
{
    public $msg;

    public function __construct($msg){
        $this->msg = json_decode($msg);
    }
    /**
     * 消息数据
     * @return object|array|string|null
     */
    public function data(): object|array|string|null
    {
        return $this->msg;
    }

    /**
     * 上报类型
     */
    public function type(){
        return @$this->msg->post_type;
    }

    /**
     * 收到事件的机器人 QQ 号
     */
    public function self_id(){
        return $this->msg->self_id;
    }

    /**
     * 发送者 QQ 号
     */
    public function user_id(){
        if(@$this->msg->user_id){
            return $this->msg->user_id;
        }
        return null;
    }

    /**
     * 消息内容
     */
    public function message(){
        if(@$this->msg->message){
            return $this->msg->message;
        }
        return null;
    }

    /**
     * 发送人信息
     */
    public function sender(){
        if(@$this->msg->sender){
            return $this->msg->sender;
        }
        return null;
    }

    /**
     * 消息类型
     */
    public function message_type(){
        if(@$this->msg->message_type){
            return $this->msg->message_type;
        }
        return null;
    }
	
	/**
	 * 通知类型
	 */
	public function notice_type(){
		if(@$this->msg->notice_type){
			return $this->msg->notice_type;
		}
		return null;
	}
	
	/**
	 * 请求类型
	 */
	public function request_type(){
		if(@$this->msg->request_type){
			return $this->msg->request_type;
		}
		return null;
	}

    public function Send($data, $action): void
    {
        sendMsg($data,$action);
    }
}
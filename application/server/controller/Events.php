<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);
namespace app\server\controller;
use GatewayWorker\Lib\Gateway;
use think\Db;
/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {

//        file_put_contents('test.log','connect success...'.$client_id,FILE_APPEND);

//        file_put_contents('test.log','来自：'.$_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'].' 的客户：'.$client_id.' 在 '.date('Y-m-d H:i:s',time()).' 与服务器连接成功!'.PHP_EOL,FILE_APPEND);
//        // 向当前client_id发送数据
        Gateway::sendToClient($client_id, json_encode(array(
            'type'      => 'init',
            'client_id' => $client_id
        )));
//        Gateway::sendToClient($client_id, "Hello $client_id\r\n");
        // 向所有人发送
        //Gateway::sendToAll("$client_id login\r\n");
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $message)
    {
        file_put_contents('test.txt',"gsay11111111........"."\r\n",FILE_APPEND);
        file_put_contents('test.log','客户：'.$client_id.' 在 '.date('Y-m-d H:i:s',time()).'  发来消息：'.$message.PHP_EOL,FILE_APPEND);
        // 向所有人发送
        $message_data = json_decode($message,true);
        if(!$message_data){
            return;
        }
        switch($message_data['type']){
            case "bind":
                $fromid = $message_data['fromid'];
                Gateway::bindUid($client_id, $fromid);
                file_put_contents('test.log',"bind:".$client_id.'-'.$fromid."\r\n",FILE_APPEND);
                return;
            case "say":
                $text = nl2br(htmlspecialchars($message_data['data']));
                $fromid = $message_data['fromid'];
                $toid = $message_data['toid'];
                $date=[
                    'type'=>'text',
                    'data'=>$text,
                    'fromid'=>$fromid,
                    'toid'=>$toid,
                    'time'=>time()
                ];
                if(Gateway::isUidOnline($toid)){
                    $date['isread']= 1;
                    Gateway::sendToUid($toid, json_encode($date));
                }else{
                    $date['isread']=0;
                }

                $date['type']="save";
                Gateway::sendToUid($fromid,json_encode($date));
//             Gateway::sendToAll(json_encode($date));
                return;

            case "say_img":
                $toid = $message_data['toid'];
                $fromid =$message_data['fromid'];
                $img_name = $message_data['data'];
                $date=[
                    'type'=>'say_img',
                    'fromid'=>$fromid,
                    'toid'=>$toid,
                    'img_name'=>$img_name
                ];
                Gateway::sendToUid($toid,json_encode($date));
                return;
            case "online":
                $toid = $message_data['toid'];
                $fromid = $message_data['fromid'];
                $status = Gateway::isUidOnline($toid);
                Gateway::sendToUid($fromid,json_encode(['type'=>"online","status"=>$status]));
                return;
            case "g_say":
                file_put_contents('test.txt',"gsay........"."\r\n",FILE_APPEND);
                $message = nl2br(htmlspecialchars($message_data['data']));
                $fromid = $message_data['fromid']; //本人
                $toid = $message_data['toid'];  //qun
                file_put_contents('test.txt',$fromid."\r\n",FILE_APPEND);
                file_put_contents('test.txt',$toid."\r\n",FILE_APPEND);
//                $res = DB::query('select user_id from group_user where group_id = '.$toid);
//                $message = 'hello,我的id：'.$fromid.',欢迎大家撩我';
//                if(false == empty(array_filter($res))){
//                    foreach ($res as $value){
                        $date=[
                            'message'=>$message,
                            'from_id'=>$fromid,
                            'group_id' =>$toid,
                            'time'=>time(),
                            'type' => "saveg"
                        ];
//                        $toids[] = $value['user_id'];
                    }
//                }
//            $this->log_err(json_encode($date));
//                Db::name('group_comm')->insertAll($date);
//                $sendData = [
//                    'data' => $date,
//                    'type' => 'saveg'
//                ];
        file_put_contents('test.txt',"send over1..."."\r\n",FILE_APPEND);
                 Gateway::sendToUid($fromid, json_encode($date));
        file_put_contents('test.txt',"send over2..."."\r\n",FILE_APPEND);
                return;
//

//        }
        //Gateway::sendToAll("$client_id said $message\r\n");
//        Gateway::sendToClient($client_id, "I've got your msg : $message --- $client_id\r\n");

    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {
        file_put_contents('test.log','客户：'.$client_id.' 断开连接'.PHP_EOL,FILE_APPEND);
        // 向所有人发送
        GateWay::sendToAll("$client_id logout\r\n");
    }
}

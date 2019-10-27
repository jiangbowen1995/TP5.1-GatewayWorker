<?php
namespace app\api\controller\v1\home;
use think\Db;
use think\Controller;
use think\Request;
use think\facade\Session;
use GatewayClient\Gateway;
use think\Route;

class User extends Controller
{
    public function __construct()
    {
        parent::__construct();

    }

    public function add_friend_view(){
        //添加好友页面
        if(empty(Session::get('id'))){
            return redirect('/login/login_view');
        }
        return $this->fetch('v1/home/user/add_friend_view');
    }

    public function add_group_view(){
        //添加群页面
        if(empty(Session::get('id'))){
            return redirect('/login/login_view');
        }
        $this->assign('fromid',Session::get('id'));
        return $this->fetch('v1/home/user/add_group_view');
    }

    public function changeNoRead(){
        $param = Request()->param();
        $from_id = $param['fromid'];
        $to_id = $param['toid'];
        Db::query("update communication set status =1 where to_id = $from_id and from_id = $to_id");
        return;
    }



    public function saveFriend()
    {
        $param = Request()->param();
        $friend_id = $param['id'];
        $master_id = Session::get('id');
        //检验ID是否存在
        $res = DB::table('user')->where('id',$friend_id)->find();
        if(empty($res)){
            return 'ID不存在';
        }
        $insertData = array(
            'user_id' => $friend_id,
            'master_id' => $master_id
        );
        Db::table('master_user')->insert($insertData);
        $userId = Db::name('master_user')->getLastInsID();
        if($userId){
            Gateway::$registerAddress = '127.0.0.1:12398';
            $date=[
                'message'=>'您好',
                'from_id'=>$friend_id,
                'to_id'=>$master_id,
                'time'=>time()
            ];
                $this->log_err('add friend failed1...'.Gateway::isUidOnline($master_id));
                if(Gateway::isUidOnline($master_id)){
                    $date['status']= 1;
                    Db::table('communication')->insert($date);
                    $Id = Db::table('communication')->getLastInsID();
                    $date['type'] = 'text';
                    Gateway::sendToUid($master_id, json_encode($date));
                }else{
                    $date['status']=0;
                    Db::table('communication')->insert($date);
                    $Id = Db::table('communication')->getLastInsID();
                }
                $this->log_err('add friend id...'.$Id);
                $date['type']="text";
                Gateway::sendToUid($friend_id,json_encode($date));

            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('新增成功', '/');
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败');
        }
    }

    public function log_err($data)
    {
        file_put_contents('test.txt',$data."\r\n",FILE_APPEND);
    }

    public function saveGroup(){
        $param = Request()->param();
        $group_id = $param['id'];
        $master_id = Session::get('id');
        //检验ID是否存在
        $res = DB::table('group')->where('group_id',$group_id)->find();
        if(empty($res)){
            return 'ID不存在';
        }
        $insertData = array(
            'group_id' => $group_id,
            'user_id' => $master_id
        );
        Db::table('group_user')->insert($insertData);
        $userId = Db::name('group_user')->getLastInsID();
        if($userId){
            Gateway::$registerAddress = '127.0.0.1:12398';
            $res = DB::query('select user_id from group_user where group_id = '.$group_id);
            $message = 'hello,我的id：'.$master_id.',欢迎大家撩我';
            if(false == empty(array_filter($res))){
                foreach ($res as $value){
                    $date[]=[
                        'message'=>$message,
                        'from_id'=>$master_id,
                        'to_id'=>$value['user_id'],
                        'group_id' =>$group_id,
                        'time'=>time(),
                        'status' => Gateway::isUidOnline($value['user_id']) ? 1 : 0
                    ];
                }
            }

            if(empty($date)){
                $date[]=[
                    'message'=>$message,
                    'from_id'=>$master_id,
                    'to_id'=>0,
                    'group_id' =>$group_id,
                    'time'=>time(),
                    'status' => 1
                ];
            }
//            $this->log_err(json_encode($date));
            Db::name('group_comm')->insertAll($date);
            //获取当前用户的client_id
//            var_dump(Gateway::isUidOnline($master_id));

//            var_dump($master_id);
            $client_id = Gateway::getClientIdByUid($master_id);
//            var_dump($client_id);
//            $this->log_err(json_encode($client_id));
            Gateway::joinGroup($client_id[0],$group_id);
            $sendData = [
                'from_id' => $master_id,
                'group_id' => $group_id,
                'messgae' => $message,
                'time' => time(),
                'type' => 'saveg'
            ];
            Gateway::sendToGroup($group_id, json_encode($sendData));
//            if(Gateway::isUidOnline($master_id)){
//                $date['status']= 1;
//                $date['type'] = 'text';
//                Gateway::sendToUid($master_id, json_encode($date));
//            }else{
//                $date['status']=0;
//            }
//            $this->log_err('add friend id...'.$Id);
//            $date['type']="text";
//            Gateway::sendToUid($friend_id,json_encode($date));
            $this->success('新增成功', '/');
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败');
        }
    }
    public function saveGroup2()
    {
        $param = Request()->param();
        $gname = $param['gname'];
        $person = $param['max_person'];
        $time = date('Y-m-d H:i:s');
        $master_id = Session::get('id');
        $insertData = array(
            'g_name' => $gname,
            'g_max_person' => $person,
            'g_creator' => $master_id,
            'g_create_time' => $time
        );
        Db::table('group')->insert($insertData);
        $userId = Db::name('group')->getLastInsID();
        if($userId){
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('新增成功', '/');
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败');
        }
    }


    public function index()
    {
        $id = Request()->param('id');
        //修改消息状态
//        Db::name('communication')
//            ->where(['to_id'=>Session::get($id),'from_id'=>$id])
//            ->data(['status' => 1])
//            ->update();
//        $res = DB::table('user')->where('id',$id)->find();
        $this->assign('fromid',Session::get('id'));
        $this->assign('toid',$id);
        return $this->fetch('v1/home/user/index');
    }

    public function g_index()
    {
        $id = Request()->param('id');
        $this->assign('fromid',Session::get('id'));
        $this->assign('toid',$id);
        return $this->fetch('v1/home/user/g_index');
    }

    public function bind()
    {
        //绑定client_id,uid
//        $client_id = Request()->param('client_id');
//        Gateway::bindUid($client_id, Session::get('id'));
//        $this->log('bind success',$client_id.'-'.Session::get('id'));
//        return;
//          file_put_contents('test.log',json_encode(Request()->param())."\r\n",FILE_APPEND);
    }


    public function save_message()
    {
        //接收消息
        $data = Request()->param();
        $insertData = array(
            'from_id' => $data['fromid'],
            'to_id' => $data['toid'],
            'message' => $data['data'],
            'status' => $data['isread'],
            'time' => $data['time']
        );
        Db::table('communication')->insert($insertData);
        $Id = Db::name('communication')->getLastInsID();
        if($Id){
            $this->log('saveMess-s',$data['fromid']);
        }else{
            $this->log('saveMess-f',$data['fromid']);
        }

    }


    public function getLastMessage($fromid,$toid){
//        whereOr('字段名','表达式','查询条件');
//        $info = Db::table('communication')->whereOr('(from_id=:fromid&&to_id=:toid)||(from_id=:fromid2&&to_id=:toid2)',['fromid'=>$fromid,'toid'=>$toid,'fromid2'=>$toid,'toid2'=>$fromid])->order('communication_id DESC')->limit(1)->find();
        $info =Db::query("select * from communication where (from_id=$fromid and to_id = $toid) or (from_id=$toid and to_id = $fromid) order  by communication_id DESC limit 1");
        if(empty(array_filter($info))){
            $info[0]['time'] = 1571906592;
            $info[0]['message'] = '';
        }
        return $info[0];
    }
    public function getUserList()
    {
        //查询好友列表
//        $id = Session::get('id');
//        $res = DB::query("select master_id,user_id from master_user where (master_id= $id or user_id = $id)");
//        foreach ($res as $value){
//            if($value['master_id'] == $id){
//                $users[] = $value['user_id'];
//            }else if($value['user_id'] == $id){
//                $users[] = $value['master_id'];
//            }
//        }
//        if(false == empty($users)){
//
//        }
        $res1 = Db::table('communication')
            ->join('user','from_id =id')
            ->where('to_id',Session::get('id'))
            ->field('to_id,from_id,message,name,pic,1 as stype')
            ->group('from_id')
            ->select();
        $res2 = DB::table('group_comm')
            ->alias('a')
            ->join('group b','a.group_id = b.group_id','left')
            ->field('g_img as pic,g_name as name,message,to_id,a.group_id as from_id,2 as stype')
            ->where('to_id',Session::get('id'))
            ->group('a.group_id')
            ->select();
        $res = array_merge($res1,$res2);
//        var_dump($res);
//        var_dump($res2);
//        $res = Db::table('master_user')
//            ->join('communication','to_id = master_id','left')
//            ->join('user','user_id=id','left')
//            ->where('master_id',Session::get('id'))
//            ->field('master_id as to_id,user_id as from_id,message,name,pic')
//            ->group('from_id')
//            ->select();
//        Db::table('table_name')->getLastSql();
//        $res1 = Db::table('master_user')
//            ->join('user','user_id = id')
//            ->where('master_id',Session::get('id'))
//            ->select();

//        $fromid = input('id');
//        $info  = Db::name('communication')->field(['fromid','toid','fromname'])->where('toid',$fromid)->group('fromid')->select();

        $rows = array_map(function($row){
            return [
                'head_url'=>$row['pic'],
                'username'=>$row['name'],
                'countNoread'=>$this->getCountNoread($row['from_id'],$row['to_id']),
                'last_message'=>$this->getLastMessage($row['from_id'],$row['to_id']),
                'chat_page'=> $row['stype'] == 1 ? '/user/messge/'.$row['from_id'] : '/user/group/'.$row['from_id']
            ];

        },$res);

        return $rows;
    }


    public function getCountNoread($fromid,$toid){
        return Db::table('communication')->where(['from_id'=>$fromid,'to_id'=>$toid,'status'=>0])->count('communication_id');
    }

    public function load(){

            $fromid = Request()->param('fromid');
            $toid =  Request()->param('toid');
            $info =Db::query("select * from communication where (from_id=$fromid and to_id = $toid) or (from_id=$toid and to_id = $fromid)");
//            return $info;
//            $count =  Db::name('communication')->where('(fromid=:fromid and toid=:toid) || (fromid=:toid1 and toid=:fromid1)',['fromid'=>$fromid,'toid'=>$toid,'toid1'=>$toid,'fromid1'=>$fromid])->count('id');
            if(count($info)>=10){
//                $message = Db::name('communication')->where('(fromid=:fromid and toid=:toid) || (fromid=:toid1 and toid=:fromid1)',['fromid'=>$fromid,'toid'=>$toid,'toid1'=>$toid,'fromid1'=>$fromid])->limit($count-10,10)->order('id')->select();
                $message =Db::query("select communication.*,a.pic as fpic,b.pic as tpic from communication left join user a on from_id = a.id left join user b on to_id = b.id where (from_id=$fromid and to_id = $toid) or (from_id=$toid and to_id = $fromid) order by communication_id  limit ".(count($info)-10).',10');

            }else{
                $message =Db::query("select communication.*,a.pic as fpic,b.pic as tpic from communication  left join user a on from_id = a.id left join user b on to_id = b.id where (from_id=$fromid and to_id = $toid) or (from_id=$toid and to_id = $fromid) order by communication_id ");
//                $message = Db::name('communication')->where('(fromid=:fromid and toid=:toid) || (fromid=:toid1 and toid=:fromid1)',['fromid'=>$fromid,'toid'=>$toid,'toid1'=>$toid,'fromid1'=>$fromid])->order('id')->select();
            }
            foreach ($message as &$value){
                $value['fpic'] = 'https://jiangbowen.cn/'.$value['fpic'];
                $value['tpic'] = 'https://jiangbowen.cn/'.$value['tpic'];
            }
            return $message;

    }

    public function get_head()
    {
//        return ['usernmae'=>'jianbowen'];

        $fromid = Request()->param('fromid');
        $toid =  Request()->param('toid');
        $map = [$fromid=>'fromid',$toid=>'toid'];
        $str_id = implode(',',[$fromid,$toid]);
        $info = DB::table('user')->whereIn('id',$str_id)->field('pic,id')->select();
        foreach ($info as $value){
            $infos[$map[$value['id']]] = 'https://jiangbowen.cn/'.$value['pic'];
        }
//        var_dump($fromid);
//        var_dump($toid);
//        var_dump($infos);

        return $infos;
    }

    public function get_name()
    {
        $toid =  Request()->param('uid');
        $info = DB::table('user')->where('id',$toid)->field('name')->find();
        return $info;
    }

    public function log($type,$data){
        file_put_contents('test.log',$type.'-----'.$data."\r\n",FILE_APPEND);
    }

}
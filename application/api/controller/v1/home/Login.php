<?php
namespace app\api\controller\v1\home;
use think\Db;
use think\Controller;
use think\Request;
use think\facade\Session;

class Login extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login_view(){
        //登陆
        return $this->fetch('v1/home/login/login');
    }

    public function reg_view(){
        //注册
//        var_dump('a');
        return $this->fetch('v1/home/login/add');
    }

    public function insert()
    {
        $param = Request()->param();
        $result = $this->validate($param,'app\api\validate\v1\home\User.reg');
        if(true !== $result){
            dump($result);
        }
        //上传图片处理
        $file = request()->file('pic');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            if(!is_dir('uploads')){
                @mkdir('uploads',0777,true);
            }
            $info = $file->move('uploads');
            if($info){
                $insertData = [
                    'name' => $param['name'],
                    'sex' => $param['sex'],
                    'age' => $param['age'],
                    'email' => $param['email'],
                    'pic' => 'uploads/'.$info->getSaveName(),
                    'password' => $param['password'],
                    'memo' => $param['memo'],
                    'mobile' => $param['mobile']
                ];
                Db::table('user')->insert($insertData);
                $userId = Db::name('user')->getLastInsID();
                if($userId){
                    //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                    $this->success('注册成功', '/login/login_view');
                } else {
                    //错误页面的默认跳转页面是返回前一页，通常不需要设置
                    $this->error('注册失败');
                }
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

    public function login()
    {
        $param = Request()->param();
        $result = $this->validate($param,'app\api\validate\v1\home\User.login');

//        $validate = Loader::validate('app\api\validate\v1\home\User');
        if(true !== $result){
            dump($result);
        }
        if(empty($param['email'])){
            return 'email is empty';
        }
        if(empty($param['password'])){
            return 'password is empty';
        }
        $res = DB::table('user')->where('email',$param['email'])->find();
        if(!empty($res)){
            $where = [
                'email' => $param['email'],
                'password' => $param['password']
            ];
            $res = DB::table('user')->where($where)->find();
            if(false == empty($res)){
                Session::set('id',$res['id']);
                Session::set('uname',$param['email']);
                $this->success('登陆成功', '/');
            }else{
                $this->error('登陆失败');
            }
        }else{
            return '邮箱不存在';
        }
    }

    public function logout(){
        Session::delete('id');
        Session::delete('uname');
        $this->redirect('/');
    }

}

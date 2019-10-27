<?php
namespace app\api\controller\v1\home;
use think\Db;
use think\Controller;
use think\facade\Request;
use think\Loader;
use think\facade\Session;
use GatewayClient\Gateway;
class Index extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        Session::set('id',1528);
    }

    public function index(){
//
        if(empty(Session::get('id'))){
//            var_dump('aaaaaa');
            $this->redirect("/login/login_view");
        }
        //查询数据库信息
//        $count = Request::instance()->param('count',10);
//        $search = Request::instance()->param('search','');
//        $res = Db::table('user')->where('name','like','%'.$search.'%')->paginate($count);
//        Db::table('master_user')->where('master_id',Session::get('id'))->select();

        //好友列表
//        $res1 = Db::table('master_user')
//            ->join('user','user_id = id')
//            ->where('master_id',Session::get('id'))
//            ->select();
//
//        //群聊列表(自己创建的群)
//        $res2 = Db::table('group')
//            ->alias('a')
//            ->join('group_user b','a.group_id = b.group_id','left')
//            ->join('user c', 'b.user_id = c.id','left')
//            ->where('a.g_creator',Session::get('id'))
//            ->select();
        $this->assign(array('fromid'=>Session::get('id')));
//        return $this->fetch('v1/home/index/index');
        return $this->fetch('v1/home/user/lists');
    }

    public function add(){
        //添加页面
        return $this->fetch('v1/home/index/add');
    }

    public function insert()
    {
        $param = Request::instance()->param();
        $validate = Loader::validate('app\api\validate\v1/home\User');
        if(!$validate->scene('login')->check($param)){
            dump($validate->getError());
        }
        //上传图片处理
        $file = request()->file('pic');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            if(!is_dir('uploads')){
                @mkdir('uploads',0777,true);
            }
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $insertData = [
                    'name' => $param['name'],
                    'sex' => $param['sex'],
                    'age' => $param['age'],
                    'email' => $param['email'],
                    'pic' => 'uploads\\'.$info->getSaveName(),
                    'password' => $param['password']
                ];
                Db::table('user')->insert($insertData);
                $userId = Db::name('user')->getLastInsID();
                if($userId){
                    //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                    $this->success('新增成功', '/');
                } else {
                    //错误页面的默认跳转页面是返回前一页，通常不需要设置
                    $this->error('新增失败');
                }
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

    public function edit($id){
        if(empty($id)){
            return 'param error';
        }
        $res = Db::table('user')->where('id','=',$id)->find();
        $this->assign('res',$res);
        return $this->fetch('v1/home/index/edit');
    }

    public function update(){
        $param = Request::instance()->param();
        $validate = Loader::validate('app\api\validate\v1/home\User');
        if(!$validate->scene('edit')->check($param)){
            dump($validate->getError());
        }
        //上传图片处理
        $file = request()->file('pic');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            if(!is_dir('uploads')){
                @mkdir('uploads',0777,true);
            }
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $updateData = [
                    'name' => $param['name'],
                    'sex' => $param['sex'],
                    'age' => $param['age'],
                    'email' => $param['email'],
                    'pic' => 'uploads\\'.$info->getSaveName(),
                ];
//                Db::table('user')->insert($insertData);
                $id = Db::table('user')->where('id','=',$param['id'])->update($updateData);
//                $userId = Db::name('user')->getLastInsID();
                if($id){
                    //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                    $this->success('编辑成功', '/');
                } else {
                    //错误页面的默认跳转页面是返回前一页，通常不需要设置
                    $this->error('编辑失败');
                }
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

    public function delete($id){

        $userId = Db::table('user')->where('id','=',$id)->delete();
        if($userId){
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('删除成功', '/');
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('删除失败');
        }
    }

}
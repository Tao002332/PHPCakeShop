<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\ResponseCode;
use App\Enums\UserDataFlagCode;
use App\Http\Controllers\Api\ApiController;
use App\Model\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use JWTAuth;


class AdminController extends ApiController
{

    public function __construct(){
        $this->middleware('admin.auth')->except('login','changePassword');
    }



    /** 管理员登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        $data= $request->json()->all();
        $status=auth('admin')->attempt($data);
        if(!$status) {
            return $this->fail(ResponseCode::USERNAME_PASSWORD_ERROR);
        } else {
            $user=auth("admin")->user();
            if($user['data_flag']==UserDataFlagCode::NOT_ACTIVE) {
                return $this->fail(ResponseCode::NOT_ACTIVE);
            } else if ($user['data_flag']==UserDataFlagCode::FORBID) {
                return $this->fail(ResponseCode::USER_FORBID);
            }
        }
        return  $this->success("登录成功",['token'=>$status]);
    }


    /** 获取用户信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInfo() {
        return $this->success("获取成功",auth("admin")->user());
    }

    /**
     * 退出登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth("admin")->logout();
        return  $this->success("退出登录");
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request) {
        $data=$request->json()->all();
        $admin=Admin::where('admin_name',$data['admin_name'])->first();
        $admin->password=bcrypt($data['password']);
        if($admin->update()) {
            return  $this->success("修改成功");
        } else {
            return  $this->fail(ResponseCode::UPDATE_ERROR);
        }
    }



    /**获取全部古雅包
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        $data= Admin::get();
        return $this->success("查询成功",$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request,Admin $admin)
    {
        //
        $data=$request->json()->all();
        $admin['admin_name']=$data['admin_name'];
        $admin['password']=bcrypt($data['password']);
        $admin['data_flag']=1;
        if($admin->save()) {
            return  $this->success("添加成功");
        } else {
            return  $this->fail(ResponseCode::INSERT_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $admin=Admin::find($id);
        return  $this->success("查询成功",$admin);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
        $data=$request->json()->all();
        $data['password']=bcrypt($data['password']);
        $admin=Admin::find($id);
        $columns =Schema::getColumnListing('tb_admins');
        $admin=$this->dynamicUpdate($data,$columns,$admin);
        if($admin->save()) {
            return  $this->success("修改成功");
        } else {
            return  $this->fail(ResponseCode::UPDATE_ERROR);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        //
        $this->changeStatus($id,UserDataFlagCode::FORBID);
        return  $this->success("封禁成功");
    }


    public function changeStatus($id,$value) {
        $admin=Admin::find($id);
        $admin->update(['data_flag'=>$value]);
    }


    /**解封
     * @param $id
     */
    public function unseal($id) {
        $this->changeStatus($id,UserDataFlagCode::OK);
        return  $this->success("解禁成功");
    }


    /**
     * 查询条件封装
     */
    public function searchCondition($data,Admin $where) {
        if(isset($data['id'])) {
            $where=$where->where('id','like','%'.$data['id'].'$');
        }
        if(isset($data['admin_name'])){
            $where=$where->where('admin_name','like','%'.$data['admin_name'].'%');
        }
        if(isset($data['data_flag'])) {
            $where=$where->where('data_flag','=',$data['data_flag']);
        }
        if(isset($data['createRange'])) {
            $where=$where->whereBetween('created_at',$data['createRange']);
        }
        if(isset($data['updateRange'])) {
            $where=$where->whereBetween('updated_at',$data['updateRange']);
        }
        $where=$where->orderBy('created_at','desc')->orderBy('id','asc');
        return $where;
    }

    /**条件查询
     * @param Request $request
     * @param Admin $admin
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request,Admin $admin) {
        $data=$request->json()->all();
        $admin=$this->searchCondition($data,$admin);
        $res=$admin->get();
        return $this->success("查询成功",$res);
    }


    /**分页条件查询
     * @param Request $request
     * @param Admin $admin
     * @param $page
     * @param $size
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPage(Request $request,Admin $admin,$page,$size) {
        $data=$request->json()->all();
        $admin=$this->searchCondition($data,$admin);
        $total=$admin->count();
        $res=$admin->offset(($page-1)*$size)->limit($size)->get();
        return $this->success("查询成功",['total'=>$total,'rows'=>$res]);
    }


}

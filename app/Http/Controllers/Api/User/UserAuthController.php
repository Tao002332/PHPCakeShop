<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\ResponseCode;
use App\Enums\UserDataFlagCode;
use App\Http\Controllers\Api\ApiController;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Auth;

class UserAuthController extends ApiController
{
    //
    public function __construct() {
        $this->middleware('user.auth')->only('getInfo','editUserInfo');
    }

    /** 管理员登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request,User $user) {
        $data= $request->json()->all();
        $status=Auth::guard()->attempt([
            'user_name'=>$data['user_name'],
            'password'=>$data['password'],
            'data_flag'=>UserDataFlagCode::OK,
        ]);
        if($status) {
            return  $this->success("登录成功",['token'=>$status]);
        } else {
            $checkUser=$user->where('user_name','=',$data['user_name'])->value('data_flag');
            if($checkUser==UserDataFlagCode::FORBID) {
                return $this->fail(ResponseCode::USER_FORBID);
            } else if($checkUser==UserDataFlagCode::NOT_ACTIVE) {
                return $this->fail(ResponseCode::NOT_ACTIVE);
            }
            return $this->fail(ResponseCode::USERNAME_PASSWORD_ERROR);
        }
    }


    /** 获取用户信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInfo() {
        return $this->success("成功",Auth::guard()->user());
    }

    /**
     * 退出登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        Auth::guard()->logout();
        return  $this->success("成功");
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request) {
        $data=$request->json()->all();
        $user=User::find($data['id']);
        $user['password']=bcrypt($data['password']);
        if($user->update()) {
            return  $this->success("修改成功");
        } else {
            return  $this->fail(ResponseCode::UPDATE_ERROR);
        }
    }


    /**发送 重置密码的邮箱
     * @param Request $request
     * @param User $user
     */
    public function handleForget(Request $request,User $user) {
        $email=$request['email'];
        $res=$user->where('email','=',$email)->get();
        $this->sendMsg('email.forget','重置密码',$res);
    }

    /**发送注册用户的邮箱
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $data=$request->json()->all();
        $data['password']=bcrypt($data['password']);
        $data['nickname']=isset($data['nickname'])?$data['nickname']:'用户'.substr(bcrypt('guigui'),1,20);
        $data['data_flag']=UserDataFlagCode::NOT_ACTIVE;
        $user=User::create($data);
        if ($user){
            $this->sendMsg('email.active','激活邮箱',$user);
            return $this->success("添加成功");
        } else {
            return  $this->fail(ResponseCode::INSERT_ERROR);
        }
    }

    public function sendMsg($view,$subject,User $user) {
        Mail::send($view,['user'=>$user],function ($m) use ($subject,$user) {
            $m->to($user['email'])->subject($subject);
        });
    }


    /**激活
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function active(Request $request,User $user) {
        $id=$request['id'];
        $nickname=$request['nickname'];
        $res=$user->where('id','=',$id)->where('nickname','=',$nickname)->get();
        $this->changeStatus($id,UserDataFlagCode::OK);
        return view('email.wait',compact('user',$res));
    }


    /**
     * 修改个人信息
     */
    public function editUserInfo(Request $request) {
        $data=$request->json()->all();
        if(auth()->user()->update(['nickname'=>$data['nickname']])) {
            return  $this->success("修改成功");
        } else {
            return  $this->fail(ResponseCode::UPDATE_ERROR);
        }
    }



}

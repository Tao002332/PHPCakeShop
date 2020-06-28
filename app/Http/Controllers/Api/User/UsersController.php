<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\UserDataFlagCode;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\CommonController;
use App\Model\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

use App\Enums\ResponseCode;

class UsersController extends ApiController
{

    public function __construct(){
        $this->middleware('admin.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data=User::get();
        return $this->success("查询成功",$data);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data=User::find($id);
        return $this->success("查询成功",$data);
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
        $user=User::find($id);
        $columns =Schema::getColumnListing('tb_user');
        $user=$this->dynamicUpdate($data,$columns,$user);
        if($user->save()) {
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
        $this->changeStatus($id,UserDataFlagCode::DELETED);
        return  $this->success("删除成功");
    }

    public function changeStatus($id,$value) {
        $user=User::find($id);
        $user->update(['data_flag'=>$value]);
    }


    /**解封
     * @param $id
     */
    public function unseal($id) {
        $this->changeStatus($id,UserDataFlagCode::OK);
        return  $this->success("解禁成功");
    }

    /**封禁
     * @param $id
     */
    public function forbid($id) {
        $this->changeStatus($id,UserDataFlagCode::FORBID);
        return  $this->success("解禁成功");
    }



    /**
     * 查询条件封装
     */
    public function searchCondition($data,User $where) {
        if(isset($data['id'])) {
            $where=$where->where('id','like','%'.$data['id'].'%');
        }
        if(isset($data['user_name'])){
            $where=$where->where('user_name','like','%'.$data['user_name'].'%');
        }
        if(isset($data['email'])){
            $where=$where->where('email','like','%'.$data['email'].'%');
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
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request,User $user) {
        $data=$request->json()->all();
        $user=$this->searchCondition($data,$user);
        $res=$user->get();
        return $this->success("查询成功",$res);
    }


    /**分页条件查询
     * @param Request $request
     * @param User $user
     * @param $page
     * @param $size
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPage(Request $request,User $user,$page,$size) {
        $data=$request->json()->all();
        $user=$this->searchCondition($data,$user);
        $total=$user->count();
        $res=$user->offset(($page-1)*$size)->limit($size)->get();
        return $this->success("查询成功",['total'=>$total,'rows'=>$res]);
    }




}

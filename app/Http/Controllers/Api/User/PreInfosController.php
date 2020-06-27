<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Model\PreInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Enums\ResponseCode;


class PreInfosController extends ApiController
{
    public function __construct() {
        $this->middleware('user.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $preInfos=PreInfo::get();
        return $this->success("查询成功",$preInfos);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //
        $data=$request->json()->all();
        $data['user_id']=auth()->user()['id'];
        if(PreInfo::create($data)) {
            return $this->success("添加成功");
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
        //
        $preInfo=PreInfo::find($id);
        return $this->success("查询成功",$preInfo);
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
        $preInfo=PreInfo::find($id);
        $columns =Schema::getColumnListing('tb_pre_info');
        $preInfo=$this->dynamicUpdate($data,$columns,$preInfo);
        if($preInfo->save()) {
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
        $res=PreInfo::where('id',$id)->delete();
        if($res) {
            return  $this->success("删除成功");
        } else {
            return  $this->fail(ResponseCode::DELETE_ERROR);
        }
    }


    /**
     * 通过uid 获取 预存信息集合
     */
    public function  findByUid() {
        $res=PreInfo::where("user_id",auth()->user()['id'])->orderBy('created_at','desc')->orderBy('id','asc')->get();
        return  $this->success("查询成功",$res);
    }



    /**
     * 查询条件封装
     */
    public function searchCondition($data,PreInfo $where) {
        if(isset($data['id'])) {
            $where=$where->where('id','like','%'.$data['id'].'%');
        }
        if(isset($data['user_id'])){
            $where=$where->where('user_id','=',$data['user_id']);
        }
        if(isset($data['phone'])){
            $where=$where->where('phone','like','%'.$data['phone'].'%');
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
     * @param PreInfo $preInfo
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request,PreInfo $preInfo) {
        $data=$request->json()->all();
        $preInfo=$this->searchCondition($data,$preInfo);
        $res=$preInfo->get();
        return $this->success("查询成功",$res);
    }


    /**分页条件查询
     * @param Request $request
     * @param PreInfo $preInfo
     * @param $page
     * @param $size
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPage(Request $request,PreInfo $preInfo,$page,$size) {
        $data=$request->json()->all();
        $preInfo=$this->searchCondition($data,$preInfo);
        $total=$preInfo->count();
        $res=$preInfo->offset(($page-1)*$size)->limit($size)->get();
        return $this->success("查询成功",['total'=>$total,'rows'=>$res]);
    }




}

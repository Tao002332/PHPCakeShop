<?php

namespace App\Http\Controllers\Api\Product;

use App\Enums\ProductDataFlagCode;
use App\Enums\ResponseCode;
use App\Http\Controllers\Api\AdminAuthController;
use App\Model\ProductCates;
use App\Model\ProductSku;
use App\Model\ProductSpu;
use App\Model\PropertyKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery\Exception;

class CatesController extends AdminAuthController
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        $data=ProductCates::get();
        return $this->success("返回集合成功",$data);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request,ProductCates $cates )
    {
        //
        $data=$request->json()->all();
        $data['data_flag']=ProductDataFlagCode::PUT_ON;
        if($cates->create($data)) {
            return $this->success("添加成功");
        } else {
            return $this->fail(ResponseCode::INSERT_ERROR);
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
        $cates=ProductCates::find($id);
        return $this->success("查询成功",$cates);
    }

    /**获取 当前id的所有 spu
     * @param $id
     */
    public function getSpus($id) {
        $cates=ProductCates::find($id);
        $spus=$cates->spus()->get();
        if($spus) {
            return $this->success("查询成功",['cates'=>$cates,'spus'=>$spus]);
        } else {
            return $this->fail(ResponseCode::QUERY_CONDITION_ERROR);
        }
    }

    /**获取 当前id的所有 key
     * @param $id
     */
    public function getKeys($id) {
        $cates=ProductCates::find($id);
        $keys=$cates->keys()->get();
        if($keys) {
            return $this->success("查询成功",['cates'=>$cates,'keys'=>$keys]);
        } else {
            return $this->fail(ResponseCode::QUERY_CONDITION_ERROR);
        }
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
        $cates=ProductCates::find($id);
        $data=$request->json()->all();
        $columns =Schema::getColumnListing('tb_product_cates');
        $cates=$this->dynamicUpdate($data,$columns,$cates);
        if($cates->save()) {
            return $this->success("更新成功");
        } else {
            return $this->fail(ResponseCode::UPDATE_ERROR);
        }

    }

    /** 下架
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        //
        $this->putOnOfPull($id,ProductDataFlagCode::PULL_OFF);
        return $this->success("下架成功");
    }


    /** 联级上架 下架
     * @param $id
     * @param $value
     */
    protected function putOnOfPull($id,$value) {
        $cates=ProductCates::find($id);
        DB::beginTransaction();
        try {
            $cates->update(['data_flag'=>$value]);
            $spus=$cates->spus()->get();
            ProductSpu::where('cate_id',$id)->update(['data_flag'=>$value]);
            PropertyKey::where('cate_id',$id)->update(['data_flag'=>$value]);
            foreach ($spus as $spu) {
                ProductSku::where('spu_id',$spu['id'])->update(['data_flag'=>$value]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**上架 置sku的状态未1
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function putOn($id) {
        $this->putOnOfPull($id,ProductDataFlagCode::PUT_ON);
        return $this->success("上架成功");
    }


    /**
     * 查询条件封装
     */
    public function searchCondition($data,ProductCates $where) {
        if(isset($data['id'])) {
            $where=$where->where('id','like','%'.$data['id'].'%');
        }
        if(isset($data['pid'])){
            $where=$where->where('pid','like','%'.$data['pid'].'%');
        }
        if(isset($data['title'])){
            $where=$where->where('title','like','%'.$data['title'].'%');
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
        $where=$where->orderBy('ord','desc')->orderBy('created_at','desc');
        return $where;
    }

    /**条件查询
     * @param Request $request
     * @param ProductCates $cates
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request,ProductCates $cates) {
        $data=$request->json()->all();
        $cates=$this->searchCondition($data,$cates);
        $res=$cates->get();
        return $this->success("查询成功",$res);
    }


    /**分页条件查询
     * @param Request $request
     * @param ProductCates $cates
     * @param $page
     * @param $size
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPage(Request $request,ProductCates $cates,$page,$size) {
        $data=$request->json()->all();
        $cates=$this->searchCondition($data,$cates);
        $total=$cates->count();
        $res=$cates->offset(($page-1)*$size)->limit($size)->get();
        return $this->success("查询成功",['total'=>$total,'rows'=>$res]);
    }
}

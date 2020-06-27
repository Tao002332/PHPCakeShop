<?php

namespace App\Http\Controllers\Api\Product;

use App\Enums\ProductDataFlagCode;
use App\Enums\ResponseCode;
use App\Http\Controllers\Api\AdminAuthController;
use App\Model\ProductSku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SkusController extends AdminAuthController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        $res=ProductSku::get();
        return $this->success("查询成功",$res);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request,ProductSku $sku)
    {
        //
       $data=$request->json()->all();
       $data['attribute_list']=json_encode($data['attribute_list']);
       $sku['data_flag']=ProductDataFlagCode::PUT_ON;
       if($sku->create($data)){
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
        $res=ProductSku::find($id);
        return $this->success("查询成功",$res);
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
        $sku=ProductSku::find($id);
        $data['attribute_list']=json_encode($data['attribute_list']);
        $columns =Schema::getColumnListing('tb_product_sku');
        $sku=$this->dynamicUpdate($data,$columns,$sku);
        if($sku->save()) {
            return $this->success("更新成功");
        } else {
            return $this->fail(ResponseCode::UPDATE_ERROR);
        }

    }

    /**更新库存
     * @param $id   库存id
     * @param $value    库存增加 1   库存减少 -1
     * @return \Illuminate\Http\JsonResponse
     */
    public function  updateStock( $id,$value) {
        $sku=ProductSku::find($id);
        $sku['stock']+=$value;
        if($sku->save()) {
            return $this->success("更新成功");
        } else {
            return $this->fail(ResponseCode::UPDATE_ERROR);
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
        $sku=ProductSku::find($id);
        if($sku->update(['data_flag'=>ProductDataFlagCode::PULL_OFF])) {
            return $this->success("下架成功");
        } else {
            return $this->fail(ResponseCode::UPDATE_ERROR);
        }
    }


    /**
     * 查询条件封装
     */
    public function searchCondition($data,ProductSku $where) {
        if(isset($data['id'])) {
            $where=$where->where('id','like','%'.$data['id'].'%');
        }
        if(isset($data['spu_id'])){
            $where=$where->where('spu_id','like','%'.$data['spu_id'].'%');
        }
        if(isset($data['priceRange'])) {
            $where=$where->whereBetween('price',$data['priceRange']);
        }
        if(isset($data['stockRange'])) {
            $where=$where->whereBetween('stock',$data['stockRange']);
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
        $where=$where->orderBy('created_at','desc');
        return $where;
    }

    /**条件查询
     * @param Request $request
     * @param ProductSku $sku
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request,ProductSku $sku) {
        $data=$request->json()->all();
        $sku=$this->searchCondition($data,$sku);
        $res=$sku->get();
        return $this->success("查询成功",$res);
    }


    /**分页条件查询
     * @param Request $request
     * @param ProductSku $sku
     * @param $page
     * @param $size
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPage(Request $request,ProductSku $sku,$page,$size) {
        $data=$request->json()->all();
        $sku=$this->searchCondition($data,$sku);
        $total=$sku->count();
        $res=$sku->offset(($page-1)*$size)->limit($size)->get();
        return $this->success("查询成功",['total'=>$total,'rows'=>$res]);
    }



}

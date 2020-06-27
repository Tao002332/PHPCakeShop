<?php

namespace App\Http\Controllers\Api\Product;

use App\Enums\ProductDataFlagCode;
use App\Enums\ResponseCode;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Controller;
use App\Model\ProductSku;
use App\Model\ProductSpu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery\Exception;

class SpusController extends AdminAuthController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        $res=ProductSpu::get();
        return $this->success("查询成功",$res);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request,ProductSpu $spu)
    {
        //
        $data=$request->json()->all();
        $spu['data_flag']=ProductDataFlagCode::PUT_ON;
        $spu['pv']=0;
        DB::beginTransaction();
        try {
            $spu->create($data);
            if(isset($data['skus']) && $data['skus']) {
                foreach ($data['skus'] as $sku) {
                    $spu->skus()->create([
                        'price'=>$sku['price'],
                        'stock'=>$sku['stock'],
                        'attribute_list'=>$sku['attribute_list'],
                        'data_flag'=>ProductDataFlagCode::PUT_ON,
                    ]);
                }
            }
            DB::commit();
            return $this->success("添加成功");
        } catch (Exception $e) {
            DB::rollBack();
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
        $spu=ProductSpu::find($id);
        if($spu) {
            return $this->success("查询成功",$spu);
        } else {
            return $this->fail(ResponseCode::QUERY_CONDITION_ERROR);
        }
    }

    /** id查询 skus 集合
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSkus($id) {
        $spu=ProductSpu::find($id);
        $skus=$spu->skus()->get();
        if($skus) {
            return $this->success("查询成功",['spu'=>$spu,'skus'=>$skus]);
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
        $data=$request->json()->all();
        $spu=ProductSpu::find($id);
        $columns =Schema::getColumnListing('tb_product_spu');
        $spu=$this->dynamicUpdate($data,$columns,$spu);
        DB::beginTransaction();
        try {
            $spu->save();
            if(isset($data['skus']) && $data['skus']) {
                ProductSku::where('spu_id',$id)->delete();
                foreach ($data['skus'] as $sku) {
                    $spu->skus()->create([
                        'price'=>$sku['price'],
                        'stock'=>$sku['stock'],
                        'attribute_list'=>$sku['attribute_list'],
                        'data_flag'=>ProductDataFlagCode::PUT_ON,
                    ]);
                }
            }
             DB::commit();
            return $this->success("修改成功");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->fail(ResponseCode::UPDATE_ERROR);
        }

    }

    public function whereIsEx($data,ProductSpu $spu) {
        $spu['cate_id']=$data['cate_id'];
        $spu['title']=$data['title'];
        $spu['desc']=$data['desc'];
        $spu['keyword']=$data['keyword'];
        $spu['img']=$data['img'];
        $spu['discount']=$data['discount'];
        $spu['price']=$data['price'];
        $spu['pd']=$data['pd'];
        $spu['expd']=$data['expd'];
        $spu['data_flag']=$data['data_flag'];
        $spu['pv']=$data['pv'];
        return $spu;
    }

    /**下架 置sku的状态未0
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        //
        return $this->putOnOfPull($id,ProductDataFlagCode::PULL_OFF);
    }


    protected function putOnOfPull($id,$value) {
        $spu=ProductSpu::find($id);
        DB::beginTransaction();
        try {
            $spu->update(['data_flag'=>$value]);
            ProductSku::where('spu_id',$id)->update(['data_flag'=>$value]);
            DB::commit();
            return $this->success("修改成功");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->fail(ResponseCode::UPDATE_ERROR);
        }
    }

    /**上架 置sku的状态未1
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function putOn($id) {
        return $this->putOnOfPull($id,ProductDataFlagCode::PUT_ON);
    }


    /**
     * 查询条件封装
     */
    public function searchCondition($data,ProductSpu $where, $ord='created_at',$by='desc') {
        if(isset($data['id'])) {
            $where=$where->where('id','like','%'.$data['id'].'%');
        }
        if(isset($data['cate_id'])){
            $where=$where->where('cate_id','=',$data['cate_id']);
        }
        if(isset($data['title'])){
            $where=$where->where('title','like','%'.$data['title'].'%');
        }
        if(isset($data['desc'])){
            $where=$where->where('desc','like','%'.$data['desc'].'%');
        }
        if(isset($data['keyword'])){
            $where=$where->whereRaw('FIND_IN_SET(?,keyword)',[$data['keyword']]);
        }
        if(isset($data['discountRange'])) {
            $where=$where->whereBetween('discount',$data['discountRange']);
        }
        if(isset($data['priceRange'])) {
            $where=$where->whereBetween('price',$data['priceRange']);
        }
        if(isset($data['pdRange'])) {
            $where=$where->whereBetween('pd',$data['pdRange']);
        }
        if(isset($data['expdRange'])) {
            $where=$where->whereBetween('expd',$data['expdRange']);
        }
        if(isset($data['data_flag'])) {
            $where=$where->where('data_flag','=',$data['data_flag']);
        }
        if(isset($data['pvRange'])) {
            $where=$where->whereBetween('pv',$data['pvRange']);
        }
        if(isset($data['createRange'])) {
            $where=$where->whereBetween('created_at',$data['createRange']);
        }
        if(isset($data['updateRange'])) {
            $where=$where->whereBetween('updated_at',$data['updateRange']);
        }
        $where=$where->orderBy($ord,$by);
        return $where;
    }

    /**条件查询
     * @param Request $request
     * @param ProductSpu $spu
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request,ProductSpu $spu) {
        $data=$request->json()->all();
        $spu=$this->searchCondition($data,$spu);
        $res=$spu->get();
        return $this->success("查询成功",$res);
    }


    /**分页条件查询
     * @param Request $request
     * @param ProductSpu $spu
     * @param $page
     * @param $size
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPage(Request $request,ProductSpu $spu,$page,$size) {
        $data=$request->json()->all();
        if(isset($data['ord'])) {
            $spu=$this->searchCondition($data,$spu,$data['ord'],$data['by']);
        } else {
            $spu=$this->searchCondition($data,$spu);
        }
        $total=$spu->count();
        $res=$spu->offset(($page-1)*$size)->limit($size)->get();
        return $this->success("查询成功",['total'=>$total,'rows'=>$res]);
    }

}

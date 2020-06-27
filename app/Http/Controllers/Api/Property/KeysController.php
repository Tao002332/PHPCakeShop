<?php

namespace App\Http\Controllers\Api\Property;

use App\Enums\ProductDataFlagCode;
use App\Enums\ResponseCode;
use App\Http\Controllers\Api\AdminAuthController;
use App\Model\PropertyKey;
use App\Model\PropertyValue;
use Composer\SelfUpdate\Keys;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery\Exception;

class KeysController extends AdminAuthController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        $data=PropertyKey::get();
        return $this->success("成功",$data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request,PropertyKey $key)
    {
        $data=$request->json()->all();
        $key['cate_id']=$data['cate_id'];
        $key['title']=$data['title'];
        $key['data_flag']=ProductDataFlagCode::PUT_ON;
        DB::beginTransaction();
        try{
            $key->save();
            if(isset($data['values'])) {
                foreach ($data['values'] as $value) {
                    $key->values()->create([
                        'value'=>$value['value'],
                        'data_flag'=>ProductDataFlagCode::PUT_ON,
                    ]);
                }
            }
            DB::commit();
            return $this->success("提交成功");
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
        $keys=PropertyKey::find($id);
        $values=$keys->values()->get();
        return $this->success("查询成功",['keys'=>$keys,'values'=>$values]);
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
        $key=PropertyKey::find($id);
        $data=$request->json()->all();
        $key['data_flag']=1;
        $columns =Schema::getColumnListing('tb_property_key');
        $key=$this->dynamicUpdate($data,$columns,$key);
        DB::beginTransaction();
        try{
            $key->save();
            if(isset($data['values'])) {
                PropertyValue::where('pv_id',$id)->delete();
                foreach ($data['values'] as $value) {
                    $key->values()->create([
                        'value'=>$value['value'],
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->putOnOfPull($id,ProductDataFlagCode::PULL_OFF);
    }


    /** 联级上架 下架
     * @param $id
     * @param $value
     */
    protected function putOnOfPull($id,$value) {
        $key=PropertyKey::find($id);
        DB::beginTransaction();
        try{
            $key->update(['data_flag'=>$value]);
            PropertyValue::where('pv_id',$id)->update(['data_flag'=>$value]);
            DB::commit();
            return $this->success("执行成功");
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
     * 通过cart——id
     * 获取keys
     * @param $id
     * @param PropertyKey $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByCateId($id,PropertyKey $key) {
        $res=$key->where('cate_id','=',$id)->get();
        return $this->success("查询成功",$res);
    }






}

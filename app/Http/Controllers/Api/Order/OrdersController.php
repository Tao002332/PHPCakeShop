<?php

namespace App\Http\Controllers\Api\Order;

use App\Enums\DeliveryTypeCode;
use App\Enums\OrderDataFlagCode;
use App\Enums\OrderStatusCode;
use App\Enums\ResponseCode;
use App\Http\Controllers\Api\ApiController;
use App\Model\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class OrdersController extends ApiController
{

    protected $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');

    public function __construct() {
        $this->middleware('admin.auth')->only('index','destroy','search','searchPage');
        $this->middleware('user.auth')->only('changeOrderStatus');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        $res=Order::get();
        return $this->success("查询成功",$res);
    }


    /**生成订单
     * @return string
     */
    protected  function generationOrderNo() {
        return  'CS'.$this->yCode[intval(date('Y')) - now()->year] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request,Order $order)
    {
        //
        $data=$request->json()->all();
        $order['order_no']=$this->generationOrderNo();
        $order['user_id']=isset($data['user_id'])?$data['user_id']:'-1';
        $order['order_status']=OrderStatusCode::NOT_PAY;
        $order['product_money']=$data['product_money'];
        $order['deliver_type']=DeliveryTypeCode::DELIVERY_IN_LOGISTICES;
        $order['recevicer']=$data['recevicer'];
        $order['recevicer_address']=$data['recevicer_address'];
        $order['recevicer_phone']=$data['recevicer_phone'];
        $order['data_flag']=OrderDataFlagCode::VALID;
        DB::beginTransaction();
        try {
            $order->save($data);
            if(isset($data['orderDetails'])) {
                foreach ($data['orderDetails'] as $orderDetail) {
                    $order->orderDetails()->create($orderDetail);
                }
            }
            DB::commit();
            return $this->success("添加成功",$order['order_no']);
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
        $order=Order::find($id);
        $orderDetails=$order->orderDetails()->get();
        return $this->success("查询成功",['order'=>$order,'orderDetails'=>$orderDetails]);
    }


    /**通过用户id查询订单
     * @return \Illuminate\Http\JsonResponse
     */
        public function findByUid() {
        $order=Order::where("user_id",auth()->user()['id'])->orderBy('created_at','desc')->orderBy('id','asc')->get();;
        return  $this->success("查询成功",$order);
    }



    /**
     * 修改订单状态
     */
    public  function  changeOrderStatus($id,$value) {
        $order=Order::find($id);
        $order['order_status']=$value;
        if($order->save()) {
            return $this->success("更新成功");
        } else {
            return $this->fail(ResponseCode::UPDATE_ERROR);
        }
    }

    /**
     * 通过 订单号和 预留手机号  查询订单
     */
    public function findOrderByOrderNoAndPhone(Request $request) {
        $data=$request->json()->all();
        $resOrder=Order::where('order_no','=',$data['order_no'])->where('recevicer_phone','=',$data['recevicer_phone'])->first();
        $order=Order::find($resOrder['id']);
        $orderDetails=$order->orderDetails()->get();
        return $this->success("查询成功",['order'=>$resOrder,'orderDetails'=>$orderDetails]);
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
        $order=Order::find($id);
        if ($order->update(['data_flag'=>OrderDataFlagCode::DELETED])) {
            return $this->success('更新成功');
        } else {
            return $this->fail(ResponseCode::UPDATE_ERROR);
        }
    }


    /**
     * 查询条件封装
     */
    public function searchCondition($data,Order $where) {
        if(isset($data['id'])) {
            $where=$where->where('id','like','%'.$data['id'].'%');
        }
        if(isset($data['order_no'])){
            $where=$where->where('order_no','=',$data['order_no']);
        }
        if(isset($data['user_id'])) {
            $where=$where->where('user_id','=',$data['user_id']);
        }
        if(isset($data['order_status'])) {
            $where=$where->where('order_status','=',$data['order_status']);
        }
        if(isset($data['product_moneyRange'])) {
            $where=$where->whereBetween('product_money',$data['product_moneyRange']);
        }
        if(isset($data['deliver_type'])) {
            $where=$where->where('deliver_type','=',$data['deliver_type']);
        }
        if(isset($data['recevicer_phone'])) {
            $where=$where->where('recevicer_phone','=',$data['recevicer_phone']);
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
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request,Order $order) {
        $data=$request->json()->all();
        $order=$this->searchCondition($data,$order);
        $res=$order->get();
        return $this->success("查询成功",$res);
    }


    /**分页条件查询
     * @param Request $request
     * @param Order $order
     * @param $page
     * @param $size
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPage(Request $request,Order $order,$page,$size) {
        $data=$request->json()->all();
        $order=$this->searchCondition($data,$order);
        $total=$order->count();
        $res=$order->offset(($page-1)*$size)->limit($size)->get();
        return $this->success("查询成功",['total'=>$total,'rows'=>$res]);
    }




}

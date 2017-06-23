<?php
/**
 * Created by PhpStorm.
 * User: admin_wu
 * Date: 2017/6/23
 * Time: 10:55
 */
namespace Admin\Controller;
use Admin\Logic\GoodsLogic;
use Think\AjaxPage;
class ActivityiconController extends BaseController{

	function index(){

		$this->display();
	}

	public function ajaxindex()
	{

		$this->assign('icon',C('activity_icon'));
		$where = 'g.show_type=0 and g.is_audit=1 and g.is_show=1 and g.is_on_sale=1';
		if(!empty(I('store_name')))
		{
			$this->assign('store_name', I('store_name'));
			$store_id = M('merchant')->where("`store_name` like '%".I('store_name')."%'")->getField('id');
			$where = "$where and g.store_id = $store_id";
		}
		if(!empty(I('icon_id')))
		{
			$icon_id = I('icon_id');
			$where = "$where and pi.type = $icon_id";
		}
		$count = M('promote_icon')->alias('pi')
			->join('INNER JOIN tp_goods g on g.goods_id = pi.goods_id ')
			->where($where)
			->count();
		$Page = new AjaxPage($count, 20);
		foreach ($where as $key => $val) {
			$Page->parameter[$key] = urlencode($val);
		}
		$show = $Page->show();
		//获取订单列表
		$goods = M('promote_icon')->alias('pi')
			->join('INNER JOIN tp_goods g on g.goods_id = pi.goods_id ')
			->where($where)
			->field('g.goods_id,g.goods_name,g.shop_price,g.prom_price,pi.type,g.store_id,g.cat_id,g.is_on_sale,g.is_show')
			->select();
		for ($i = 0; $i < count($goods); $i++) {
			$name = M('merchant')->where('`id`=' . $goods[$i]['store_id'])->field('store_name')->find();
			$cat_name = M('goods_category')->where('`id`=' . $goods[$i]['cat_id'])->field('name')->find();
			$goods[$i]['cat_name'] = $cat_name['name'];
			$goods[$i]['store_name'] = $name['store_name'];
		}

		$this->assign('goods', $goods);
		$this->assign('page', $show);// 赋值分页输出
		$this->display();
	}

	function goods_info(){

		$this->display();
	}

	public function search_goods(){
		$condition['is_on_sale'] = 1;
		$condition['is_special'] = 0;
		$condition['the_raise'] = 0;
		$condition['is_show'] = 1;
		$condition['is_audit'] = 1;
		$condition['show_type'] =0;
		if(I('goods_id')){
			$goods_id = I('goods_id');
			$this->assign('goods_id', $goods_id);
			$condition['goods_id'] = $goods_id;
		}
		if (!empty($_REQUEST['keywords'])) {
			$this->assign('keywords', I('keywords'));
			$condition['the_raise'] = array('like',I('keywords'));
		}
		if(!empty(I('store_name'))){
			$store_name = I('store_name');
			$this->assign('store_name', I('store_name'));
			$store_id = M('merchant')->where("`store_name` like '%".$store_name."%'")->getField('id');
			$condition['store_id'] = $store_id;
		}

		$promote_icon_goods_id = M('promote_icon')->field('goods_id')->select();
		if(!empty($promote_icon_goods_id)){
			$condition['goods_id'] =array('not in',array_column($promote_icon_goods_id,'goods_id'));
		}

		$count = M('goods')->where($condition)->count();
		$Page = new \Think\Page($count, 10);
		$goodsList = M('goods')
			->where($condition)
			->order('addtime DESC')
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();

		for($i=0;$i<count($goodsList);$i++)		{
			$store_name = M('merchant')->where('`id`='.$goodsList[$i]['store_id'])->field('store_name')->find();
			$goodsList[$i]['store_name'] = $store_name['store_name'];
		}

		$show = $Page->show();//分页显示输出
		$this->assign('page', $show);//赋值分页输出
		$this->assign('goodsList', $goodsList);
		$tpl = I('get.tpl', 'search_goods');
		$this->display($tpl);
	}

	public function goods_save()
	{
		for($i=0;$i<count($_POST['goods_id']);$i++){
			$res = M('promote_icon')->data(array('goods_id'=>$_POST['goods_id'][$i],'type'=>$_POST['icon_id'],'src'=>$i,'create_time'=>time()))->add();
			redislist("goods_refresh_id", $_POST['goods_id'][$i]);
		}
		if($res){
			$this->success("添加成功",U('Activityicon/index'));
		}else{
			$this->success("添加失败",U('Activityicon/goods_info'));
		}
	}
}
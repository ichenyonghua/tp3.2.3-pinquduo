<?php
/**
 * ashop
 */

namespace Home\Controller;


use Think\Controller;

class ApiController extends Controller {
    /*
     * 获取地区
     */
    public function getRegion(){
        $parent_id = I('get.parent_id');
        $selected = I('get.selected',0);        
        $data = M('region')->where("parent_id=$parent_id")->select();
        $html = '';
        if($data){
            foreach($data as $h){
            	if($h['id'] == $selected){
            		$html .= "<option value='{$h['id']}' selected>{$h['name']}</option>";
            	}
                $html .= "<option value='{$h['id']}'>{$h['name']}</option>";
            }
        }
        echo $html;
    }
    

    public function getTwon(){
    	$parent_id = I('get.parent_id');
    	$data = M('region')->where("parent_id=$parent_id")->select();
    	$html = '';
    	if($data){
    		foreach($data as $h){
    			$html .= "<option value='{$h['id']}'>{$h['name']}</option>";
    		}
    	}
    	if(empty($html)){
    		echo '0';
    	}else{
    		echo $html;
    	}
    }

    /*
     * 获取地区
     */
    public function get_category(){
        $parent_id = I('get.parent_id'); // 商品分类 父id
            $list = M('goods_category')->where("parent_id = $parent_id")->select();

        foreach($list as $k => $v)
            $html .= "<option value='{$v['id']}'>{$v['name']}</option>";
        exit($html);
    }

	public function get_category_haitao(){
		$parent_id = I('get.parent_id'); // 商品分类 父id
		$list = M('haitao')->where("parent_id = $parent_id")->select();

		foreach($list as $k => $v)
			$html .= "<option value='{$v['id']}'>{$v['name']}</option>";
		exit($html);
	}
}
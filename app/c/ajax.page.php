<?php
/**
 * 通用异步请求控制器
 * @author 齐迹  email:smpss2012@gmail.com
 */
class c_ajax extends base_c{
	
	public function pagegetregion() {
		$aJson = array ();
		$regionObj = new m_region ();
		$iPid = ( int ) $_REQUEST ['parent_id'];
		$exce = ( int ) $_REQUEST ['exce'];
		$aRegions = $regionObj->select ( array ("parent_id" => $iPid ), '*', '', 'order by region_id asc' )->items;
		if (empty ( $iPid )) {
			echo json_encode ( array () );
			exit ();
		}
		if ($aRegions) {
			foreach ( $aRegions as $aRegion ) {
				$r = array ('region_id' => $aRegion ['region_id'], 'region_name' => $aRegion ['region_name'] );
				$aJson [] = $r;
			}
			echo json_encode ( $aJson );
		} else {
			if ($exce) {
				$aRegions = $regionObj->select ( array ("parent_id" => $iPid ), '*', '', 'order by region_id asc' )->items;
				if (! empty ( $aRegions )) {
					$r = array ('region_id' => $aRegions ['region_id'], 'region_name' => $aRegions ['region_name'] );
					echo json_encode ( array ($r ) );
				} else {
					echo json_encode ( array () );
				}
			} else {
				echo json_encode ( array () );
			}
		}
	}
	/**
	 * 生成条形码图片
	 * @param array $inPath
	 */
	public function pagebarcode($inPath){
		$code = $_REQUEST['code'];
		$SBarcode = new SBarcode();
		$SBarcode->genBarCode($code,'png','2','');
	}
	/**
	 * 随机生成一组条形码
	 */
	public function pagegetbarcode($inPath){
		$code = base_Constant::BARCODE.base_Utils::random(4,1);
		$SBarcode = new SBarcode();
		$code = $SBarcode->_ean13CheckDigit($code);
		if(strlen($code)==13){
			$imgsrc = $this->createUrl("/ajax/barcode")."?code={$code}";
			return json_encode(array("code"=>$code,"imgsrc"=>$imgsrc));
		}else{
			return $this->pagegetbarcode($inPath);
		}
	}
	/**
	 * 反馈
	 */
	function pagefb($inPath){
		$data['nickname'] = base_Utils::getstr($_POST['n']);
		$data['email'] = base_Utils::getstr($_POST['e']);
		$data['intro'] = base_Utils::getstr($_POST['i']);
		if(!$data['nickname'] or !$data['email'] or !$data['intro']){
			return json_encode(array('state'=>0,'msg'=>'数据不完整'));
		}
		$data['type'] = (int)$_POST['t']?(int)$_POST['t']:3;
		$data['ip'] = base_Utils::getip();
		$data['dateline'] = time();
		$data['replay'] = '';
		$data['replaytime'] = '';
		$mailcont = "<p>用户名：{$data['nickname']}</p><p>邮箱：{$data['email']}</p><p>内容：{$data['intro']}</p>";
		$fb = new m_msg();
		if($fb->insert($data)){
			$mail = new SaeMail();
			$ret = $mail->quickSend( '5709775@qq.com' , $data['nickname'].'给我反馈信息' , $mailcont , 's9775@sina.cn' , 'richan' );
			//var_dump($mail->errno(), $mail->errmsg());
			return json_encode(array('state'=>1,'msg'=>'提交成功'));
		}
		return json_encode(array('state'=>0,'msg'=>'提交失败！请检查内容是否合法'));
	}
	/**
	 * 反馈列表
	 */
	function pagefblist($inPath){
		$page = (int)$_POST['p']?(int)$_POST['p']:1;
		$fb = new m_msg();
		$fb->setLimit(5);
		$fb->setPage($page);
		$list = $fb->select('replaytime>0')->items;
		if($list){
			$params['list'] = $list;
			return json_encode(array('state'=>1,'html'=>$this->render ( 'fb.html', $params )));
		}else{
			return json_encode(array('state'=>0,'html'=>''));
		}
	}
}
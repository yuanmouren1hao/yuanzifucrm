<?php
/**
 * 反馈表数据模型
 * @author 齐迹  email:smpss2012@gmail.com
 */
class m_msg extends base_m {
	public function primarykey() {
		return 'id';
	}
	public function tableName() {
		return base_Constant::TABLE_PREFIX . 'msg';
	}

}
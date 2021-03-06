<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
$id = intval($_GPC['id']);

$account = pdo_fetch("SELECT * FROM ".tablename('wechats')." WHERE weid = '$id'");
if (empty($account)) {
	message('抱歉，帐号不存在或是已经被删除', create_url('accound/display'), 'error');
}
$modules = array();
//获取全部规则
$rules = pdo_fetchall("SELECT id, module FROM ".tablename('rule')." WHERE weid = '{$account['weid']}'");
if (!empty($rules)) {
	foreach ($rules as $index => $rule) {
		$deleteid[] = $rule['id'];
		if (empty($modules[$rule['module']])) {
			$file = IA_ROOT . '/source/modules/'.$rule['module'].'/module.php';
			if (file_exists($file)) {
				include_once $file;
			}
			$modules[$rule['module']] = module($rule['module']);
		}
		$modules[$rule['module']]->ruleDeleted($rule['id']);
	}
	pdo_delete('rule', "id IN ('".implode("','", $deleteid)."')");
}

pdo_delete('wechats', array('weid' => $id));
pdo_delete('wechats_modules', array('weid' => $id));

message('公众帐号信息删除成功！', create_url('account/display'));


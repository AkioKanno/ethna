<?php
/**
 *	{$project_id}_Controller.php
 *
 *	@package	{$project_id}
 *
 *	$Id$
 */

/** ���ץꥱ�������١����ǥ��쥯�ȥ� */
define('BASE', dirname(dirname(__FILE__)));

// include_path������(���ץꥱ�������ǥ��쥯�ȥ���ɲ�)
$app = BASE . "/app";
$lib = BASE . "/lib";
ini_set('include_path', ini_get('include_path') . ":$app:$lib");


/** ���ץꥱ�������饤�֥��Υ��󥯥롼�� */
include_once('Ethna/Ethna.php');
include_once('{$project_id}_Error.php');

/**
 *	{$project_id}���ץꥱ�������Υ���ȥ������
 *
 *	@author		your name
 *	@access		public
 *	@package	{$project_id}
 */
class {$project_id}_Controller extends Ethna_Controller
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	string	���ץꥱ�������ID
	 */
	var	$appid = '{$application_id}';

	/**
	 *	@var	array	forward���
	 */
	var $forward = array(
		/*
		 *	TODO: ������forward��򵭽Ҥ��Ƥ�������
		 *
		 *	�����㡧
		 *
		 *	'index'			=> array(
		 *		'preforward_name'	=> 'IndexClass',
		 *	),
		 */
	);

	/**
	 *	@var	array	action���
	 */
	var $action = array(
		/*
		 *	TODO: ������action����򵭽Ҥ��Ƥ�������
		 */
		'index'				=> array(),
	);

	/**
	 *	@var	array	soap action���
	 */
	var $soap_action = array(
		/*
		 *	TODO: ������SOAP���ץꥱ��������Ѥ�action�����
		 *	���Ҥ��Ƥ�������
		 *	�����㡧
		 *
		 *	'sample'			=> array(),
		 */
	);

	/**
	 *	@var	array	���饹���
	 */
	var $class = array(
		/*
		 *	TODO: ���ꥯ�饹�������饹��SQL���饹�򥪡��С��饤��
		 *	�������ϲ����Υ��饹̾��˺�줺���ѹ����Ƥ�������
		 */
		'config'        => 'Ethna_Config',
		'logger'        => 'Ethna_Logger',
		'sql'           => 'Ethna_AppSQL',
	);

	/**
	 *	@var	array	�ޥ͡��������
	 */
	var $manager = array(
		/*
		 *	TODO: �����˥��ץꥱ�������Υޥ͡����㥪�֥������Ȱ�����
		 *	���Ҥ��Ƥ�������
		 *
		 *	�����㡧
		 *
		 *	'um'	=> 'User',
		 */
	);

	/**
	 *	@var	array	smarty modifier���
	 */
	var $smarty_modifier_plugin = array(
		/*
		 *	TODO: �����˥桼�������smarty modifier�����򵭽Ҥ��Ƥ�������
		 *
		 *	�����㡧
		 *
		 *	'smarty_modifier_foo_bar',
		 */
	);

	/**
	 *	@var	array	smarty function���
	 */
	var $smarty_function_plugin = array(
		/*
		 *	TODO: �����˥桼�������smarty function�����򵭽Ҥ��Ƥ�������
		 *
		 *	�����㡧
		 *
		 *	'smarty_function_foo_bar',
		 */
	);

	/**#@-*/

	/**
	 *	���ܻ��Υǥե���ȥޥ�������ꤹ��
	 *
	 *	@access	protected
	 *	@param	object	Smarty	$smarty	�ƥ�ץ졼�ȥ��󥸥󥪥֥�������
	 */
	function _setDefaultMacro(&$smarty)
	{
		$smarty->assign_by_ref('session_name', session_name());
		$smarty->assign_by_ref('session_id', session_id());

		/* ������ե饰(true/false) */
		if ($this->session->isStart()) {
			$smarty->assign_by_ref('login', $this->session->isStart());
		}
	}
}
?>

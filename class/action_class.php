<?php
/**
 *	action_class.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

/**
 *	action�¹ԥ��饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_ActionClass
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	object	Ethna_Backend		backend���֥�������
	 */
	var $backend;

	/**
	 *	@var	object	Ethna_Config		���ꥪ�֥�������	
	 */
	var $config;

	/**
	 *	@var	object	Ethna_I18N			i18n���֥�������
	 */
	var $i18n;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������
	 */
	var $action_error;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������(��ά��)
	 */
	var $ae;

	/**
	 *	@var	object	Ethna_ActionForm	action form���֥�������
	 */
	var $action_form;

	/**
	 *	@var	object	Ethna_ActionForm	action form���֥�������(��ά��)
	 */
	var $af;

	/**
	 *	@var	object	Ethna_Session		���å���󥪥֥�������
	 */
	var $session;

	/**#@-*/

	/**
	 *	Ethna_ActionClass�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	object	Ethna_Backend	$backend	backend���֥�������
	 */
	function Ethna_ActionClass(&$backend)
	{
		$c =& $backend->getController();
		$this->backend =& $backend;
		$this->config =& $this->backend->getConfig();
		$this->i18n =& $this->backend->getI18N();
		$this->db =& $this->backend->getDB();

		$this->action_error =& $this->backend->getActionError();
		$this->ae =& $this->action_error;

		$this->action_form =& $this->backend->getActionForm();
		$this->af =& $this->action_form;

		$this->session =& $this->backend->getSession();

		// Ethna_AppManager���֥������Ȥ�����
		$manager_list = $c->getManagerList();
		foreach ($manager_list as $k => $v) {
			$this->$k = $backend->getManager($v);
		}
	}

	/**
	 *	�ӥ��ͥ����å��¹����ν���(���å��������å����ե������ͥ����å���)��Ԥ�
	 *
	 *	@access	public
	 *	@return	string	Forward̾(null�ʤ����ｪλ)
	 */
	function prepare()
	{
		return null;
	}

	/**
	 *	action����
	 *
	 *	@access	public
	 *	@return	string	Forward̾
	 */
	function perform()
	{
		return null;
	}

	/**
	 *	����ɽ��������
	 *
	 *	@access	public
	 */
	function preforward()
	{
	}
}

/**
 *	���ޥ�ɥ饤��action�¹ԥ��饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_CLI_ActionClass extends Ethna_ActionClass
{
	/**
	 *	action����
	 *
	 *	@access	public
	 */
	function Perform()
	{
		parent::Perform();
		$_SERVER['REMOTE_ADDR'] = "0.0.0.0";
		$_SERVER['HTTP_USER_AGENT'] = "";
	}
}

/**
 *	AMF(Flash Remoting)action�¹ԥ��饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_AMF_ActionClass
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	object	Ethna_Backend		backend���֥�������
	 */
	var $backend;

	/**
	 *	@var	object	Ethna_Config		���ꥪ�֥�������	
	 */
	var $config;

	/**
	 *	@var	object	Ethna_I18N			i18n���֥�������
	 */
	var $i18n;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������
	 */
	var $action_error;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������(��ά��)
	 */
	var $ae;

	/**
	 *	@var	object	Ethna_Session		���å���󥪥֥�������
	 */
	var $session;

	/**
	 *	@var	array	�᥽�å����
	 */
	var $method;

	/**#@-*/

	/**
	 *	Ethna_AMF_ActionClass�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	object	Ethna_Backend	$backend	backend���֥�������
	 */
	function Ethna_AMF_ActionClass()
	{
		$c =& $GLOBALS['controller'];
		$this->backend =& $c->getBackend();
		$this->config =& $this->backend->getConfig();
		$this->i18n =& $this->backend->getI18N();

		$this->action_error =& $this->backend->getActionError();
		$this->ae =& $this->action_error;

		$this->session =& $this->backend->getSession();
	}

	/**
	 *	Credential�إå��˴�Ť��ƥ��å���������Ԥ�
	 *
	 *	@access	private
	 *	@param	string	$user_id	Credential�إå��Υ桼��ID
	 *	@param	string	$password	Credential�إå��Υѥ����(���饤����ȤϤ����˥��å����ID�����ꤹ��)
	 */
	function _authenticate($user_id, $password)
	{
		if ($this->session != null) {
			// already authenticated
			return;
		}

		$c =& $this->backend->getController();

		session_id($password);
		$this->session =& new Session($c->getAppId(), $this->backend->getTmpdir(), $this->ae);
	}
}
?>

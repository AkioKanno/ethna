<?php
// vim: foldmethod=marker
/**
 *	Ethna_AMF_ActionClass.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_AMF_ActionClass
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
// }}}
?>

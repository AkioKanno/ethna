<?php
// vim: foldmethod=marker
/**
 *	action_class.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_ActionClass
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
// }}}

// {{{ Ethna_List_ActionClass
/**
 *	�ꥹ��ɽ�������������쥯�饹�μ���
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_List_ActionClass extends Ethna_ActionClass
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	int		ɽ�����ϥ��ե��å�
	 */
	var	$offset = 0;

	/**
	 *	@var	int		ɽ�����
	 */
	var	$count = 25;

	/**
	 *	@var	array	�����оݹ��ܰ���
	 */
	var	$search_list = array();

	/**
	 *	@var	string	ɽ���оݥ��饹̾
	 */
	var	$class_name = null;

	/**#@-*/

	/**
	 *	�ꥹ��ɽ������������������
	 *
	 *	@access	public
	 *	@return	string		Forward��(���ｪλ�ʤ�null)
	 */
	function prepare()
	{
		return null;
	}

	/**
	 *	admin_stock_brand_index���������μ���
	 *
	 *	@access	public
	 *	@return	string	����̾
	 */
	function perform()
	{
		return null;
	}

	/**
	 *	����������
	 *
	 *	@access	public
	 */
	function preforward()
	{
		// ɽ�����ե��å�/�������
		$this->offset = $this->af->get('offset');
		if ($this->offset == "") {
			$this->offset = 0;
		}
		if (intval($this->af->get('count')) > 0) {
			$this->count = intval($this->af->get('count'));
		}

		// �������
		$filter = array();
		foreach ($this->search_list as $key) {
			if ($this->af->get("s_$key") != "") {
				$filter[$key] = $this->af->get("s_$key");
			}
		}

		// TODO: �����Ⱦ��
		$sort = array();

		// ɽ�����ܰ���
		for ($i = 0; $i < 2; $i++) {
			list($total, $obj_list) = $this->um->getObjectList($this->class_name, $filter, $sort, $this->offset, $this->count);
			if (count($obj_list) == 0 && $this->offset >= $total) {
				$this->offset = 0;
				continue;
			}
			break;
		}

		$r = array();
		foreach ($obj_list as $obj) {
			$value = $obj->getNameObject();
			$value = $this->_fixNameObject($value);
			$r[] = $value;
		}
		$list_name = sprintf("%s_list", strtolower(preg_replace('/(.)([A-Z])/', '\\1_\\2', $this->class_name)));
		$this->af->setApp($list_name, $r);

		// �ʥӥ��������
		$this->af->setApp('nav', $this->_getNavigation($total, $obj_list));
		$this->af->setAppNE('query', $this->_getQueryParameter());

		// �������ץ����
		$this->_setQueryOption();
	}

	/**
	 *	ɽ�����ܤ�������
	 *
	 *	@access	protected
	 */
	function _fixNameObject($obj)
	{
		return $obj;
	}
	
	/**
	 *	�ʥӥ�������������������
	 *
	 *	@access	private
	 *	@param	int		$total		��������
	 *	@param	array	$list		�������
	 *	@return	array	�ʥӥ�������������Ǽ��������
	 */
	function _getNavigation($total, &$list)
	{
		$nav = array();
		$nav['offset'] = $this->offset;
		$nav['from'] = $this->offset + 1;
		if ($total == 0) {
			$nav['from'] = 0;
		}
		$nav['to'] = $this->offset + count($list);
		$nav['total'] = $total;
		if ($this->offset > 0) {
			$prev_offset = $this->offset - $this->count;
			if ($prev_offset < 0) {
				$prev_offset = 0;
			}
			$nav['prev_offset'] = $prev_offset;
		}
		if ($this->offset + $this->count < $total) {
			$next_offset = $this->offset + count($list);
			$nav['next_offset'] = $next_offset;
		}
		$nav['direct_link_list'] = Ethna_Util::getDirectLinkList($total, $this->offset, $this->count);

		return $nav;
	}

	/**
	 *	�������ܤ���������
	 *
	 *	@access	protected
	 */
	function _setQueryOption()
	{
	}

	/**
	 *	�������Ƥ��Ǽ����GET��������������
	 *
	 *	@access	private
	 *	@param	array	$search_list	�����оݰ���
	 *	@return	string	�������Ƥ��Ǽ����GET����
	 */
	function _getQueryParameter()
	{
		$query = "";

		foreach ($this->search_list as $key) {
			$value = $this->af->get("s_$key");
			if (is_array($value)) {
				foreach ($value as $v) {
					$query .= "&s_$key" . "[]=" . urlencode($v);
				}
			} else {
				$query .= "&s_$key=" . urlencode($value);
			}
		}

		return $query;
	}
}
// }}}

// {{{ Ethna_CLI_ActionClass
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
// }}}

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

<?php
// vim: foldmethod=marker
/**
 *	Ethna_ViewClass.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_ViewClass
/**
 *	view���饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_ViewClass
{
	/**#@+
	 *	@access	private
	 */

	/**	@var	object	Ethna_Backend		backend���֥������� */
	var $backend;

	/**	@var	object	Ethna_Config		���ꥪ�֥�������	*/
	var $config;

	/**	@var	object	Ethna_I18N			i18n���֥������� */
	var $i18n;

	/**	@var	object	Ethna_ActionError	��������󥨥顼���֥������� */
	var $action_error;

	/**	@var	object	Ethna_ActionError	��������󥨥顼���֥�������(��ά��) */
	var $ae;

	/**	@var	object	Ethna_ActionForm	���������ե����४�֥������� */
	var $action_form;

	/**	@var	object	Ethna_ActionForm	���������ե����४�֥�������(��ά��) */
	var $af;

	/**	@var	object	Ethna_Session		���å���󥪥֥������� */
	var $session;

	/**	@var	string	����̾ */
	var $forward_name;

	/**	@var	string	������ƥ�ץ졼�ȥե�����̾ */
	var $forward_path;

	/**#@-*/

	/**
	 *	Ethna_ViewClass�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	object	Ethna_Backend	$backend	backend���֥�������
	 *	@param	string	$forward_name	�ӥ塼�˴�Ϣ�դ����Ƥ�������̾
	 *	@param	string	$forward_path	�ӥ塼�˴�Ϣ�դ����Ƥ���ƥ�ץ졼�ȥե�����̾
	 */
	function Ethna_ViewClass(&$backend, $forward_name, $forward_path)
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

		$this->forward_name = $forward_name;
		$this->forward_path = $forward_path;
	}

	/**
	 *	����ɽ��������
	 *
	 *	�ƥ�ץ졼�Ȥ����ꤹ���ͤǥ���ƥ����Ȥ˰�¸���ʤ���Τ�
	 *	���������ꤹ��(��:���쥯�ȥܥå�����)
	 *
	 *	@access	public
	 */
	function preforward()
	{
	}

	/**
	 *	����̾���б�������̤���Ϥ���
	 *
	 *	�ü�ʲ��̤�ɽ���������������ä˥����С��饤�ɤ���ɬ�פ�̵��
	 *	(preforward()�Τߥ����С��饤�ɤ�����ɤ�)
	 *
	 *	@access	public
	 */
	function forward()
	{
		$smarty =& $this->_getTemplateEngine();
		$this->_setDefault($smarty);
		$smarty->display($this->forward_path);
	}

    /**
     *  ���ꤵ�줿�ե�������ܤ��б�����ե�����̾(w/ �������)���������
     *
     *  @access public
     */
    function getFormName($name, $params)
    {
    }

    /**
     *  ���ꤵ�줿�ե�������ܤ��б�����ե����ॿ�����������
     *
     *  @access public
     */
    function getFormInput($name, $params)
    {
    }

	/**
	 *	Smarty���֥������Ȥ��������
	 *
	 *	@access	protected
	 *	@return	object	Smarty	Smarty���֥�������
	 */
	function &_getTemplateEngine()
	{
		$c =& $this->backend->getController();
		$smarty =& $c->getTemplateEngine();

		$form_array =& $this->af->getArray();
		$app_array =& $this->af->getAppArray();
		$app_ne_array =& $this->af->getAppNEArray();
		$smarty->assign_by_ref('form', $form_array);
		$smarty->assign_by_ref('app', $app_array);
		$smarty->assign_by_ref('app_ne', $app_ne_array);
		$message_list = Ethna_Util::escapeHtml($this->ae->getMessageList());
		$smarty->assign_by_ref('errors', $message_list);
		if (isset($_SESSION)) {
			$tmp_session = Ethna_Util::escapeHtml($_SESSION);
			$smarty->assign_by_ref('session', $tmp_session);
		}
		$smarty->assign('script', basename($_SERVER['PHP_SELF']));
		$smarty->assign('request_uri', htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES));

		return $smarty;
	}

	/**
	 *	�����ͤ����ꤹ��
	 *
	 *	@access	protected
	 *	@param	object	Smarty	Smarty���֥�������
	 */
	function _setDefault(&$smarty)
	{
	}
}
// }}}
?>

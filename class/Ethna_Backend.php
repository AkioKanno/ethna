<?php
// vim: foldmethod=marker
/**
 *	Ethna_Backend.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_Backend
/**
 *	�Хå�����ɽ������饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_Backend
{
	/**#@+
	 *	@access		private
	 */

	/**
	 *	@var	object	Ethna_Controller	controller���֥�������
	 */
	var	$controller;

	/**
	 *	@var	object	Ethna_Controller	controller���֥�������($controller�ξ�ά��)
	 */
	var	$ctl;

	/**
	 *	@var	object	Ethna_Config		���ꥪ�֥�������
	 */
	var	$config;

	/**
	 *	@var	object	Ethna_I18N			i18n���֥�������
	 */
	var $i18n;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������
	 */
	var $action_error;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������($action_error�ξ�ά��)
	 */
	var $ae;

	/**
	 *	@var	object	Ethna_ActionForm	action form���֥�������
	 */
	var $action_form;

	/**
	 *	@var	object	Ethna_ActionForm	action form���֥�������($action_form�ξ�ά��)
	 */
	var $af;

	/**
	 *	@var	object	Ethna_Session		���å���󥪥֥�������
	 */
	var $session;

	/**
	 *	@var	array	Ethna_DB���֥������Ȥ��Ǽ��������
	 */
	var $db;

	/**
	 *	@var	object	Ethna_Logger		�����֥�������
	 */
	var $logger;

	/**
	 *	@var	array	�ޥ͡����㥪�֥������ȥ���å���
	 */
	var $manager = array();

	/**#@-*/


	/**
	 *	Ethna_Backend���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	object	Ethna_Controller	&$controller	����ȥ��饪�֥�������
	 */
	function Ethna_Backend(&$controller)
	{
		// ���֥������Ȥ�����
		$this->controller =& $controller;
		$this->ctl =& $this->controller;

		$this->config =& $controller->getConfig();
		$this->i18n =& $controller->getI18N();

		$this->action_error =& $controller->getActionError();
		$this->ae =& $this->action_error;
		$this->action_form =& $controller->getActionForm();
		$this->af =& $this->action_form;

		$this->session =& $controller->getSession();
		$this->db = array();
		$this->logger =& $this->controller->getLogger();

		// �ޥ͡����㥪�֥������Ȥ�����
		$manager_list = $controller->getManagerList();
		foreach ($manager_list as $key => $value) {
			$class_name = $controller->getAppId() . "_" . ucfirst(strtolower($value)) . 'Manager';
			$this->manager[$value] =& new $class_name($this);
		}

		foreach ($manager_list as $key => $value) {
			foreach ($manager_list as $k => $v) {
				if ($v == $value) {
					/* skip myself */
					continue;
				}
				$this->manager[$value]->$k =& $this->manager[$v];
			}
		}
	}

	/**
	 *	controller���֥������ȤؤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	object	Ethna_Controller	controller���֥�������
	 */
	function &getController()
	{
		return $this->controller;
	}

	/**
	 *	���ꥪ�֥������ȤؤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	object	Ethna_Config		���ꥪ�֥�������
	 */
	function &getConfig()
	{
		return $this->config;
	}

	/**
	 *	���ץꥱ�������ID���֤�
	 *
	 *	@access	public
	 *	@return	string	���ץꥱ�������ID
	 */
	function getAppId()
	{
		return $this->controller->getAppId();
	}

	/**
	 *	I18N���֥������ȤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	object	Ethna_I18N	i18n���֥�������
	 */
	function &getI18N()
	{
		return $this->i18n;
	}

	/**
	 *	action error���֥������ȤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	object	Ethna_ActionError	action error���֥�������
	 */
	function &getActionError()
	{
		return $this->action_error;
	}

	/**
	 *	action form���֥������ȤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	object	Ethna_ActionForm	action form���֥�������
	 */
	function &getActionForm()
	{
		return $this->action_form;
	}

	/**
	 *	�����֥������ȤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	object	Ethna_Logger	�����֥�������
	 */
	function &getLogger()
	{
		return $this->logger;
	}

	/**
	 *	���å���󥪥֥������ȤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	object	Ethna_Session	���å���󥪥֥�������
	 */
	function &getSession()
	{
		return $this->session;
	}

	/**
	 *	�ޥ͡����㥪�֥������ȤؤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	object	Ethna_AppManager	�ޥ͡����㥪�֥�������
	 */
	function &getManager($type)
	{
		if (isset($this->manager[$type])) {
			return $this->manager[$type];
		}
		return null;
	}

	/**
	 *	���ץꥱ�������Υ١����ǥ��쥯�ȥ���������
	 *
	 *	@access	public
	 *	@return	string	�١����ǥ��쥯�ȥ�Υѥ�̾
	 */
	function getBasedir()
	{
		return $this->controller->getBasedir();
	}

	/**
	 *	���ץꥱ�������Υƥ�ץ졼�ȥǥ��쥯�ȥ���������
	 *
	 *	@access	public
	 *	@return	string	�ƥ�ץ졼�ȥǥ��쥯�ȥ�Υѥ�̾
	 */
	function getTemplatedir()
	{
		return $this->controller->getTemplatedir();
	}

	/**
	 *	���ץꥱ������������ǥ��쥯�ȥ���������
	 *
	 *	@access	public
	 *	@return	string	����ǥ��쥯�ȥ�Υѥ�̾
	 */
	function getEtcdir()
	{
		return $this->controller->getDirectory('etc');
	}

	/**
	 *	���ץꥱ�������Υƥ�ݥ��ǥ��쥯�ȥ���������
	 *
	 *	@access	public
	 *	@return	string	�ƥ�ݥ��ǥ��쥯�ȥ�Υѥ�̾
	 */
	function getTmpdir()
	{
		return $this->controller->getDirectory('tmp');
	}

	/**
	 *	���ץꥱ�������Υƥ�ץ졼�ȥե������ĥ�Ҥ��������
	 *
	 *	@access	public
	 *	@return	string	�ƥ�ץ졼�ȥե�����γ�ĥ��
	 */
	function getTemplateext()
	{
		return $this->controller->getExt('tpl');
	}

	/**
	 *	���߽�����Υ��饤����ȼ��̤��������
	 *
	 *	@access	public
	 *	@return	int		���饤����ȼ���
	 */
	function getClientType()
	{
		return $this->controller->getClientType();
	}

	/**
	 *	������Ϥ���
	 *
	 *	@access	public
	 *	@param	int		$level		����٥�(LOG_DEBUG, LOG_NOTICE...)
	 *	@param	string	$message	����å�����(printf����)
	 */
	function log($level, $message)
	{
		$args = func_get_args();
		if (count($args) > 2) {
			array_splice($args, 0, 2);
			$message = vsprintf($message, $args);
		}
		$this->logger->log($level, $message);
	}

	/**
	 *	�Хå�����ɽ�����¹Ԥ���
	 *
	 *	@access	public
	 *	@param	string	$action_name	�¹Ԥ��륢��������̾��
	 *	@return	mixed	(string):Forward̾(null�ʤ�forward���ʤ�) Ethna_Error:���顼
	 */
	function perform($action_name)
	{
		$forward_name = null;

		$action_class_name = $this->controller->getActionClassName($action_name);
		if ($action_class_name == null) {
			return null;
		} else if (class_exists($action_class_name) == false) {
			return Ethna::raiseError(E_APP_UNDEFINED_ACTIONCLASS, "̤����Υ�������󥯥饹[%s]", $action_class_name);
		}
		$action_class =& new $action_class_name($this);

		// ���������μ¹�
		$forward_name = $action_class->prepare();
		if ($forward_name != null) {
			return $forward_name;
		}

		$forward_name = $action_class->perform();

		return $forward_name;
	}

	/**
	 *	����ɽ����������Ԥ�
	 *
	 *	@access	public
	 *	@param	string	$forward_name	���������̾
	 *	@param	array	$forward_obj	���������
	 */
	function preforward($forward_name)
	{
		$class_name = $this->controller->getViewClassName($forward_name);
		if ($class_name == null) {
			return null;
		}
		$view_class =& new $class_name($this, $forward_name);
		$view_class->preforward();
	}

	/**
	 *	DB���֥������Ȥ��֤�
	 *
	 *	@access	public
	 *	@param	string	$type		DB����(Ethna_Controller::db���Ф����)
	 *	@return	mixed	Ethna_DB:DB���֥������� null:DSN����ʤ� Ethna_Error:���顼
	 */
	function &getDB($type = "")
	{
		$key =& $this->_getDB($type);
		if (Ethna::isError($key)) {
			return $key;
		}
		if (isset($this->db[$key]) && $this->db[$key] != null) {
			return $this->db[$key];
		}

		$dsn = $this->controller->getDSN($type);
		if ($dsn == "") {
			// DB��³����
			return null;
		}

		$this->db[$key] =& new Ethna_DB($dsn, false, $this->controller);
		$r = $this->db[$key]->connect();
		if (Ethna::isError($r)) {
			$this->db[$key] = null;
			return $r;
		}

		register_shutdown_function(array($this, 'shutdownDB'));

		return $this->db[$key];
	}

	/**
	 *	DB���֥�������(����)���������
	 *
	 *	@access	public
	 *	@return	mixed	array:Ethna_DB���֥������Ȥΰ��� Ethan_Error:(�����줫��İʾ����³��)���顼
	 *	@todo	respect access controlls
	 */
	function getDBlist()
	{
		$r = array();
		foreach ($this->controller->db as $key => $value) {
			$db =& $this->getDB($key);
			if (Ethna::isError($db)) {
				return $r;
			}
			$elt = array();
			$elt['db'] =& $db;
			$elt['type'] = $value;
			$elt['varname'] = "db";
			if ($key != "") {
				$elt['varname'] = sprintf("db_%s", strtolower($key));
			}
			$r[] = $elt;
		}
		return $r;
	}

	/**
	 *	DB���ͥ����������Ǥ���
	 *
	 *	@access	public
	 */
	function shutdownDB()
	{
		foreach (array_keys($this->db) as $key) {
			if ($this->db[$key] != null && $this->db[$key]->isValid()) {
				$this->db[$key]->disconnect();
				unset($this->db[$key]);
			}
		}
	}

	/**
	 *	DB�ȥ�󥶥������򳫻Ϥ���
	 *
	 *	@access	public
	 *	@deprecated
	 */
	function begin()
	{
		$db =& $this->db[""];
		if ($db == null || $db->isValid() == false) {
			$this->log(LOG_WARNING, "begin() with inactive DB object");
		}
		$db->begin();
	}

	/**
	 *	DB�ȥ�󥶥����������Ǥ���
	 *
	 *	@access	public
	 *	@deprecated
	 */
	function rollback()
	{
		$db =& $this->db[""];
		if ($db == null || $db->isValid() == false) {
			$this->log(LOG_WARNING, "rollback() with inactive DB object");
		}
		$db->rollback();
	}

	/**
	 *	DB�ȥ�󥶥������򥳥ߥåȤ���
	 *
	 *	@access	public
	 *	@deprecated
	 */
	function commit()
	{
		$db =& $this->db[""];
		if ($db == null || $db->isValid() == false) {
			$this->log(LOG_WARNING, "commit() with inactive DB object");
		}
		$db->commit();
	}

	/**
	 *	���ꤵ�줿DB���̤��б�����DB���֥������Ȥ��Ǽ���������ѿ����������
	 *
	 *	@access	private
	 *	@param	string	$type	DB����
	 *	@return	mixed	string:�����ѿ�̾ Ethna_Error:������DB����
	 */
	function &_getDB($type = "")
	{
		$r = $this->controller->getDB($type);
		if (is_null($r)) {
			return Ethna::raiseError(E_DB_INVALIDTYPE, "̤�����DB����[%s]", $type);
		}

		if ($type == "") {
			$key = "";
		} else {
			$key = sprintf("%s", strtolower($type));
		}

		return $key;
	}
}
// }}}
?>

<?php
/**
 *	action_error.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

/**
 *	���顼���饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_Error
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	int		���顼��٥�
	 */
	var $level;

	/**
	 *	@var	int		���顼������
	 */
	var $code;

	/**
	 *	@var	string	���顼��å�����
	 */
	var $message;

	/**
	 *	@var	array	���顼��å���������
	 */
	var $message_arg_list;

	/**
	 *	@var	array	�桼������ɲþ���
	 */
	var $info;

	/**
	 *	@var	object	Ethna_I18N	i18n���֥�������
	 */
	var $i18n;

	/**
	 *	@var	object	Ethna_Logger	logger���֥�������
	 */
	var $logger;

	/**#@-*/

	/**
	 *	Ethna_Error���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	int		$level				���顼��٥�
	 *	@param	int		$code				���顼������
	 *	@param	string	$message			���顼��å�����(+����)
	 *	@param	array	$message_arg_list	���顼��å���������
	 */
	function Ethna_Error($level, $code, $message, $message_arg_list = array())
	{
		$this->controller =& $GLOBALS['controller'];
		$this->i18n =& $this->controller->getI18N();
		$this->logger =& $this->controller->getLogger();

		$this->level = $level;
		$this->code = $code;
		$this->message = $message;
		$this->message_arg_list = $message_arg_list;
		$this->info = array();

		// ��
		list ($log_level, $dummy) = Ethna_Logger::errorLevelToLogLevel($level);
		$message = $this->getMessage();
		$this->logger->log($log_level, sprintf("[APP(%d)] %s", $code, $message == null ? "(no message)" : $message));
	}

	/**
	 *	level�ؤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	int		���顼������
	 */
	function getLevel()
	{
		return $this->level;
	}

	/**
	 *	code�ؤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	int		���顼������
	 */
	function getCode()
	{
		return $this->code;
	}

	/**
	 *	message�ؤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	array	���顼��å�����
	 */
	function getMessage()
	{
		$tmp_message = $this->i18n->get($this->message);
		$tmp_message_arg_list = array();
		for ($i = 0; $i < count($this->message_arg_list); $i++) {
			$tmp_message_arg_list[] = $this->i18n->get($this->message_arg_list[$i]);
		}
		return vsprintf($tmp_message, $tmp_message_arg_list);
	}

	/**
	 *	message�����ؤΥ�������(R)
	 *
	 *	@access	public
	 *	@param	int		message��������ǥå���
	 *	@return	mixed	message����
	 */
	function getInfo($n)
	{
		if (isset($this->message_arg_list[$n])) {
			return $this->message_arg_list[$n];
		} else {
			return null;
		}
	}

	/**
	 *	message�Ȥ��ΰ�����ù��������֤�
	 *
	 *	@access	public
	 *	@return	array	���顼��å�����, ���顼��å���������
	 */
	function getMessage_Raw()
	{
		return array($this->message, $this->message_arg_list);
	}

	/**
	 *	�桼���������ؤΥ�������(R)
	 *
	 *	@access	public
	 *	@param	string	$key	�桼��������󥭡�
	 *	@return	mixed	$key�ǻ��ꤵ�줿�桼���������
	 */
	function get($key)
	{
		if (isset($this->info[$key])) {
			return $this->info[$key];
		} else {
			return null;
		}
	}

	/**
	 *	�桼���������ؤΥ�������(W)
	 *
	 *	@access	public
	 *	@param	string	$key	�桼��������󥭡�
	 *	@param	mixed	$value	�桼�����������
	 */
	function set($key, $value)
	{
		$this->info[$key] = $value;
	}
}

/**
 *	���ץꥱ������󥨥顼���饹
 *
 *	���Υ��饹��Ethna_ActoinError���饹�ǤΤ����Ѥ����(��ȯ�Ԥ���������Τ�
 *	Ethna_Error���饹�Τ�)
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_AppError extends Ethna_Error
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	string	���顼��ȯ�������ե��������̾
	 */
	var $name;

	/**
	 *	@var	object	Ethna_ActionForm	action form���֥�������
	 */
	var $action_form;

	/**#@-*/

	/**
	 *	Ethna_AppError���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	int		$level		���顼��٥�
	 *	@param	int		$code		���顼������
	 *	@param	string	$name		���顼��ȯ�������ե��������(���פʤ�null)
	 *	@param	string	$message	���顼��å�����
	 *	@param	string	$message	���顼��å���������
	 *	@param	bool	$logging	���顼�����ϥե饰
	 */
	function Ethna_AppError($level, $code, $name, $message, $message_arg_list, $logging = true)
	{
		$this->controller =& $GLOBALS['controller'];
		$this->i18n =& $this->controller->getI18N();
		$this->logger =& $this->controller->getLogger();
		$this->action_form =& $this->controller->getActionForm();

		$this->level = $level;
		$this->code = $code;
		$this->name = $name;
		$this->message = $message;
		$this->message_arg_list = $message_arg_list;

		// ��
		if ($logging) {
			list ($log_level, $dummy) = Ethna_Logger::errorLevelToLogLevel($level);
			$message = $this->getMessage();
			$this->logger->log($log_level, sprintf("[USER(%d)-%s] %s", $code, $name == null ? "(no name)" : $name, $message == null ? "(no message)" : $message));
		}
	}

	/**
	 *	name�ؤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	string	���顼��ȯ�������ե��������̾
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 *	message�ؤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	array	���顼��å�����(�Ȥ��ΰ���)
	 */
	function getMessage()
	{
		$message = parent::getMessage();

		// �ޥ������
		$form_name = $this->action_form->getName($this->getName());
		$message = str_replace("{form}", $form_name, $message);

		return $message;
	}
}

/**
 *	���ץꥱ������󥨥顼�������饹
 *
 *	@access		public
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@package	Ethna
 */
class Ethna_ActionError
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	array	Ethna_Error���顼���֥������Ȥΰ���
	 */
	var $error_list = array();

	/**#@-*/

	/**
	 *	Ethna_ActionError���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 */
	function Ethna_ActionError()
	{
	}

	/**
	 *	Ethna_AppError���֥������Ȥ�����/�ɲä���
	 *
	 *	@access	public
	 *	@param	int		$code		���顼������
	 *	@param	string	$name		���顼��ȯ�������ե��������̾(���פʤ�null)
	 *	@param	string	$message	���顼��å�����(+����)
	 */
	function add($code, $name, $message)
	{
		$message_arg_list = array_slice(func_get_args(), 3);
		$app_error =& new Ethna_AppError(E_USER_NOTICE, $code, $name, $message, $message_arg_list);
		$this->error_list[] =& $app_error;
	}

	/**
	 *	Ethna_Error���֥������Ȥ��ɲä���
	 *
	 *	@access	public
	 *	@param	object	Ethna_Error	$error	���顼���֥�������
	 *	@param	string				$name	���顼���б�����ե��������̾(���פʤ�null)
	 */
	function addObject(&$error, $name = null)
	{
		list($message, $message_arg_list) = $error->getMessage_Raw();
		$app_error =& new Ethna_AppError($error->getLevel(), $error->getCode(), $message, $message_arg_list, false);
		$this->error_list[] =& $app_error;
	}

	/**
	 *	���顼���֥������Ȥο����֤�
	 *
	 *	@access	public
	 *	@return	int		���顼���֥������Ȥο�
	 */
	function count()
	{
		return count($this->error_list);
	}

	/**
	 *	���顼���֥������Ȥο����֤�(count()�᥽�åɤΥ����ꥢ��)
	 *
	 *	@access	public
	 *	@return	int		���顼���֥������Ȥο�
	 */
	function length()
	{
		return count($this->error_list);
	}

	/**
	 *	��Ͽ���줿���顼���֥������Ȥ����ƺ������
	 *
	 *	@access	public
	 */
	function clear()
	{
		$this->error_list = array();
	}

	/**
	 *	���ꤵ�줿�ե�������ܤ˥��顼��ȯ�����Ƥ��뤫�ɤ������֤�
	 *
	 *	@access	public
	 *	@param	string	$name	�ե��������̾
	 *	@return	bool	true:���顼��ȯ�����Ƥ��� false:���顼��ȯ�����Ƥ��ʤ�
	 */
	function isError($name)
	{
		foreach ($this->error_list as $error) {
			if (strcasecmp($error->getName(), $name) == 0) {
				return true;
			}
		}
		return false;
	}

	/**
	 *	���ꤵ�줿�ե�������ܤ��б����륨�顼��å��������֤�
	 *
	 *	@access	public
	 *	@param	string	$name	�ե��������̾
	 *	@return	string	���顼��å�����(���顼��̵������null)
	 */
	function getMessage($name)
	{
		foreach ($this->error_list as $error) {
			if (strcasecmp($error->getName(), $name) == 0) {
				return $error->getMessage();
			}
		}
		return null;
	}

	/**
	 *	���顼���֥������Ȥ�����ˤ����֤�
	 *
	 *	@access	public
	 *	@return	array	���顼���֥������Ȥ�����
	 */
	function getErrorList()
	{
		return $this->error_list;
	}

	/**
	 *	���顼��å�����������ˤ����֤�
	 *
	 *	@access	public
	 *	@return	array	���顼��å�����������
	 */
	function getMessageList()
	{
		$message_list = array();

		foreach ($this->error_list as $error) {
			$message_list[] = $error->getMessage();
		}
		return $message_list;
	}
}
?>

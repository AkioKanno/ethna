<?php
// vim: foldmethod=marker
/**
 *	Ethna_Error.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_Error
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
// }}}
?>

<?php
// vim: foldmethod=marker
/**
 *	Ethna_AppError.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_AppError
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
// }}}
?>

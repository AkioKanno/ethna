<?php
// vim: foldmethod=marker
/**
 *	Ethna_ActionError.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

include_once(ETHNA_BASE . '/class/Ethna_Error.php');
include_once(ETHNA_BASE . '/class/Ethna_AppError.php');

// {{{ Ethna_ActionError
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
		$app_error =& new Ethna_AppError($error->getLevel(), $error->getCode(), $name, $message, $message_arg_list, false);
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
// }}}
?>

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
 *	���ץꥱ������󥨥顼���饹
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
	 *	@var	string	���顼��ȯ�������ե��������̾
	 */
	var $name;

	/**
	 *	@var	object	Ethna_I18N	i18n���֥�������
	 */
	var $i18n;

	/**#@-*/


	/**
	 *	Ethna_Error���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	int		$code		���顼������
	 *	@param	string	$name		���顼��ȯ�������ե��������(���פʤ�null)
	 *	@param	string	$message	���顼��å�����(+����)
	 */
	function Ethna_Error($code, $name, $message)
	{
		$this->code = $code;
		$this->name = $name;
		$this->message = $message;
		$this->message_arg_list = array_slice(func_get_args(), 3);
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
		$message_arg_list = array_merge(array($this->message), $this->message_arg_list);
		$eval_statement = "return sprintf(";
		for ($i = 0; $i < count($message_arg_list); $i++) {
			if ($i > 0) {
				$eval_statement .= ", ";
			}
			$eval_statement .= "\$this->i18n->get(\$message_arg_list[$i])";
		}
		$eval_statement .= ");";
		return eval($eval_statement);
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

	/**
	 *	@var	object	Ethna_I18N	i18n���֥�������
	 */
	var $i18n;

	/**#@-*/

	/**
	 *	Ethna_ActionError���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	object	Ethna_I18N	$i18n	i18n���֥�������
	 */
	function Ethna_ActionError(&$i18n)
	{
		$this->i18n =& $i18n;
	}

	/**
	 *	���顼���֥������Ȥ�����/�ɲä���
	 *
	 *	@access	public
	 *	@param	int		$code		���顼������
	 *	@param	string	$name		���顼��ȯ�������ե��������̾(���פʤ�null)
	 *	@param	string	$message	���顼��å�����(+����)
	 */
	function add($code, $name, $message)
	{
		$message_arg_list = array_slice(func_get_args(), 3);
		$eval_statement = "\$error = new Ethna_Error(\$code, \$name, \$message";
		for ($i = 0; $i < count($message_arg_list); $i++) {
			$eval_statement .= ", \$message_arg_list[$i]";
		}
		$eval_statement .= ");";
		eval($eval_statement);
		$this->error_list[] =& $error;
	}

	/**
	 *	���顼���֥������Ȥ��ɲä���
	 *
	 *	@access	public
	 *	@param	object	Ethna_Error	$error	���顼���֥�������
	 */
	function addObject(&$error)
	{
		$this->error_list[] =& $error;
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
	 *	���顼���֥������Ȥο����֤�(Count()�᥽�åɤΥ����ꥢ��)
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

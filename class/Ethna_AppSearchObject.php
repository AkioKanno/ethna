<?php
// vim: foldmethod=marker
/**
 *	Ethna_AppSearchObject.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_AppSearchObject
/**
 *	���ץꥱ������󥪥֥������ȸ�����說�饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_AppSearchObject
{
	/**#@+
	 *	@access	private
	 */

	/**	@var	string	������ */
	var $value;

	/**	@var	int		������� */
	var $condition;

	/**#@-*/


	/**
	 *	Ethna_AppSearchObject�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	string	$value		������
	 *	@param	int		$condition	�������(OBJECT_CONDITION_NE,...)
	 */
	function Ethna_AppSearchObject($value, $condition)
	{
		$this->value = $value;
		$this->condition = $condition;
	}
}
// }}}
?>

<?php
// vim: foldmethod=marker
/**
 *	Ethna_Action_Info.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_Action_Info
/**
 *	__ethna_info__���������μ���
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_Action_Info extends Ethna_ActionClass
{
	/**
	 *	__ethna_info__����������������
	 *
	 *	@access	public
	 *	@return	string		Forward��(���ｪλ�ʤ�null)
	 */
	function prepare()
	{
		return null;
	}

	/**
	 *	__ethna_info__���������μ���
	 *
	 *	@access	public
	 *	@return	string	����̾
	 */
	function perform()
	{
		return '__ethna_info__';
	}

	/**
	 *	����������
	 *
	 *	@access	public
	 */
	function preforward()
	{
		$em =& new Ethna_InfoManager($this->backend);

		$this->af->setApp('ethna_version', ETHNA_VERSION);
	}
}
// }}}
?>

<?php
/**
 *	{$project_prefix}.php
 *
 *	@package	{$project_id}
 *
 *	$Id$
 */

/**
 *	index���������μ���
 *
 *	@author		yourname
 *	@access		public
 *	@package	{$project_id}
 */
class IndexClass extends Ethna_ActionClass
{
	/**
	 *	index����������������
	 *
	 *	@access	public
	 *	@return	string		Forward��(���ｪλ�ʤ�null)
	 */
	function prepare()
	{
		return null;
	}

	/**
	 *	index���������μ���
	 *
	 *	@access	public
	 *	@return	string	����̾
	 */
	function perform()
	{
		return 'index';
	}

	/**
	 *	����������
	 *
	 *	@access	public
	 */
	function preforward()
	{
	}
}
?>

<?php
/**
 *	{$project_prefix}.php
 *
 *	@package	{$project_id}
 *
 *	$Id$
 */

/**
 *	index�ե�����μ���
 *
 *	@author		yourname
 *	@access		public
 *	@package	{$project_id}
 */
class {$project_id}_Form_Index extends Ethna_ActionClass
{
	/**
	 *	@access	private
	 *	@var	array	�ե����������
	 */
	var	$form = array(
	);
}

/**
 *	index���������μ���
 *
 *	@author		yourname
 *	@access		public
 *	@package	{$project_id}
 */
class {$project_id}_Action_Index extends Ethna_ActionClass
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

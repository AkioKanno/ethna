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
class {$project_id}_Form_Index extends Ethna_ActionForm
{
	/**
	 *	@access	private
	 *	@var	array	�ե����������
	 */
	var	$form = array(
		/*
		 *	TODO: ���Υ�������󤬻��Ѥ���ե�����������򵭽Ҥ��Ƥ�������
		 *
		 *	������(type��������Ƥ����ǤϾ�ά��ǽ)��
		 *
		 *	'sample' => array(
		 *		'name'			=> '����ץ�',		// ɽ��̾
		 *		'required'      => true,			// ɬ�ܥ��ץ����(true/false)
		 *		'min'           => null,			// �Ǿ���
		 *		'max'           => null,			// ������
		 *		'regexp'        => null,			// ʸ�������(����ɽ��)
		 *		'custom'        => null,			// �᥽�åɤˤ������å�
		 *		'convert'       => null,			// �����ͼ�ư�Ѵ����ץ����
		 *		'form_type'		=> FORM_TYPE_TEXT	// �ե����෿
		 *		'type'          => VAR_TYPE_INT,	// �����ͷ�
		 *	),
		 */
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

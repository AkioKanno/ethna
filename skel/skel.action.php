<?php
/**
 *	{$action_path}
 *
 *	@author		yourname
 *	@package	{$project_id}
 *	@version	$Id$
 */

/**
 *	{$action_name}�ե�����μ���
 *
 *	@author		yourname
 *	@access		public
 *	@package	{$project_id}
 */
class {$action_form} extends Ethna_ActionForm
{
	/**
	 *	@access	private
	 *	@var	array	�ե����������
	 */
	var	$form = array(
		/*
		'sample' => array(
			'name'			=> '����ץ�',		// ɽ��̾
			'required'      => true,			// ɬ�ܥ��ץ����(true/false)
			'min'           => null,			// �Ǿ���
			'max'           => null,			// ������
			'regexp'        => null,			// ʸ�������(����ɽ��)
			'custom'        => null,			// �᥽�åɤˤ������å�
			'convert'       => null,			// �����ͼ�ư�Ѵ����ץ����
			'form_type'		=> FORM_TYPE_TEXT	// �ե����෿
			'type'          => VAR_TYPE_INT,	// �����ͷ�
		),
		*/
	);
}

/**
 *	{$action_name}���������μ���
 *
 *	@author		yourname
 *	@access		public
 *	@package	{$project_id}
 */
class {$action_class} extends Ethna_ActionClass
{
	/**
	 *	{$action_name}����������������
	 *
	 *	@access	public
	 *	@return	string		Forward��(���ｪλ�ʤ�null)
	 */
	function prepare()
	{
		return null;
	}

	/**
	 *	{$action_name}���������μ���
	 *
	 *	@access	public
	 *	@return	string	����̾
	 */
	function perform()
	{
		return '{$action_name}';
	}
}
?>

<?php
/**
 *	{$project_id}_Filter_ExecutionTime.php
 *
 *	@author		{$author}
 *	@package	{$project_id}
 *	@version	$Id$
 */

/**
 *	�¹Ի��ַ�¬�ե��륿�μ���
 *
 *	@author		{$author}
 *	@access		public
 *	@package	{$project_id}
 */
class {$project_id}_Filter_ExecutionTime extends Ethna_Filter
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	int		���ϻ���
	 */
	var	$stime;

	/**#@-*/


	/**
	 *	�¹����ե��륿
	 *
	 *	@access	public
	 */
	function preFilter()
	{
		$stime = explode(' ', microtime());
		$stime = $stime[1] + $stime[0];
		$this->stime = $stime;
	}

	/**
	 *	�¹Ը�ե��륿
	 *
	 *	@access	public
	 */
	function postFilter()
	{
		$etime = explode(' ', microtime());
		$etime = $etime[1] + $etime[0];
		$time   = round(($etime - $this->stime), 4);

		print "\n<!-- page was processed in $time seconds -->\n";
	}
}
?>

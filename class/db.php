<?php
/**
 *	db.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

/**
 *	DB���饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 *	@todo		MySQL�ʳ��б�
 */
class Ethna_DB
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	object	DB				PEAR DB���֥�������
	 */
	var $db;

	/**
	 *	@var	object	Ethna_AppSQL	SQL���֥�������
	 */
	var $sql;

	/**
	 *	@var	string	DSN
	 */
	var $dsn;

	/**
	 *	@var	bool	��³��³�ե饰
	 */
	var $persistent;

	/**
	 *	@var	string	���顼��å�����
	 */
	var	$message;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������
	 */
	var	$action_error;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������(��ά��)
	 */
	var	$ae;

	/**
	 *	@var	array	DB�ȥ�󥶥��������������å�
	 */
	var	$transaction = array();

	/**#@-*/


	/**
	 *	Ethna_DB���饹�Υ��󥹥ȥ饯��
	 */
	function Ethna_DB($dsn, $persistent, &$controller)
	{
		$this->dsn = $dsn;
		$this->persistent = $persistent;
		$this->message = null;
		$this->action_error =& $controller->getActionError();
		$this->ae =& $this->action_error;

		$this->db =& DB::Connect($dsn, $persistent);
		if (DB::isError($this->db)) {
			trigger_error(sprintf("db connect error: %s", mysql_error()), E_USER_ERROR);
		}
		$this->sql =& $controller->getSQL();
	}

	/**
	 *	DB��³�����Ǥ���
	 *
	 *	@access	public
	 */
	function disconnect()
	{
		$this->db->disconnect();
	}

	/**
	 *	DB��³���֤��֤�
	 *
	 *	@access	public
	 *	@return	bool	true:���� false:���顼
	 */
	function isValid()
	{
		if (DB::isError($this->db)) {
			$this->message = $this->db->GetMessage();
			return false;
		}
		return true;
	}

	/**
	 *	�ǿ��Υ��顼��å��������֤�
	 *
	 *	@access	public
	 *	@return	string	���顼��å�����
	 */
	function getMessage()
	{
		return $this->message;
	}

	/**
	 *	�������ȯ�Ԥ���
	 *
	 *	@access	public
	 *	@param	string	$query	SQLʸ
	 *	@return	object	DB_Result	��̥��֥�������
	 */
	function &query($query)
	{
		return $this->_query($query, false);
	}

	/**
	 *	�������ȯ�Ԥ���(�ƥ��ȥ⡼��)
	 *
	 *	@access	public
	 *	@param	string	$query	SQLʸ
	 *	@return	object	DB_Result	��̥��֥�������
	 */
	function &query_test($query)
	{
		return $this->_query($query, true);
	}

	/**
	 *	SQLʸ���ꥯ�����ȯ�Ԥ���
	 *
	 *	@access	public
	 *	@param	string	$sqlid		SQL-ID
	 *	@return	object	DB_Result	��̥��֥�������
	 */
	function &sqlquery($sqlid)
	{
		$args = func_get_args();
		array_shift($args);
		$query = $this->sql->get($sqlid, $args);

		return $this->_query($query, false);
	}

	/**
	 *	SQLʸ���ꥯ�����ȯ�Ԥ���(�ƥ��ȥ⡼��)
	 *
	 *	@access	public
	 *	@param	string	$sqlid		SQL-ID
	 *	@return	object	DB_Result	��̥��֥�������
	 */
	function &sqlquery_test($sqlid)
	{
		$args = func_get_args();
		array_shift($args);
		$query = $this->sql->get($sqlid, $args);

		return $this->_query($query, true);
	}

	/**
	 *	SQLʸ���������
	 *	
	 *	@access	public
	 *	@param	string	$sqlid		SQL-ID
	 *	@return	string	SQLʸ
	 */
	function sql($sqlid)
	{
		$args = func_get_args();
		array_shift($args);
		$query = $this->sql->get($sqlid, $args);

		return $query;
	}

	/**
	 *	ľ���INSERT�ˤ��ID���֤�
	 *
	 *	@access	public
	 *	@return	int		ľ���INSERT�ˤ���������줿ID
	 */
	function getInsertId()
	{
		return mysql_insert_id($this->db->connection);
	}

	/**
	 *	�ơ��֥���å�����
	 *
	 *	@access	public
	 *	@param	mixed	��å��оݥơ��֥�̾
	 *	@return	object	DB_Result	��̥��֥�������
	 */
	function lock($tables)
	{
		$this->message = null;

		$sql = "";
		foreach (to_array($tables) as $table) {
			if ($sql != "") {
				$sql .= ", ";
			}
			$sql .= "$table WRITE";
		}

		return $this->query("LOCK TABLES $sql;");
	}

	/**
	 *	�ơ��֥�Υ�å����������
	 *
	 *	@access	public
	 *	@return	object	DB_Result	��̥��֥�������
	 */
	function unlock()
	{
		$this->message = null;
		return $this->query("UNLOCK TABLES;");
	}

	/**
	 *	DB�ȥ�󥶥������򳫻Ϥ���
	 *
	 *	@access	public
	 */
	function begin()
	{
		if (count($this->transaction) > 0) {
			$this->transaction[] = true;
			return;
		}

		$this->query('BEGIN;');
		$this->transaction[] = true;
	}

	/**
	 *	DB�ȥ�󥶥����������Ǥ���
	 *
	 *	@access	public
	 */
	function rollback()
	{
		if (count($this->transaction) == 0) {
			return;
		} else if (count($this->transaction) > 1) {
			array_pop($this->transaction);
			return;
		}

		$this->query('ROLLBACK;');
		array_pop($this->transaction);
	}

	/**
	 *	DB�ȥ�󥶥�������λ����
	 *
	 *	@access	public
	 */
	function commit()
	{
		if (count($this->transaction) == 0) {
			return;
		} else if (count($this->transaction) > 1) {
			array_pop($this->transaction);
			return;
		}

		$this->query('COMMIT;');
		array_pop($this->transaction);
	}

	/**
	 *	�������ȯ�Ԥ���
	 *
	 *	@access	private
	 *	@param	string	$query	SQLʸ
	 *	@param	bool	$test	�ƥ��ȥ⡼�ɥե饰(true:���顼���֥������Ȥ��ɲä���ʤ�)
	 *	@return	object	DB_Result	��̥��֥�������
	 */
	function &_query($query, $test = false)
	{
		$this->message = null;

		$r =& $this->db->query($query);
		if (DB::isError($r)) {
			if ($test == false) {
				// ���곰��SQL���顼
				trigger_error(sprintf("db error: %s [%s]", mysql_error(), $query), E_USER_ERROR);
				return null;
			} else {
				// �������SQL���顼(duplicate entry��)
				return $r;
			}
		}
		return $r;
	}
}
?>

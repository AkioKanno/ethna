<?php
// vim: foldmethod=marker
/**
 *	Ethna_DB.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_DB
/**
 *	DB���饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
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
	 *	@var	object	Ethna_Logger	�����֥�������
	 */
	var $logger;

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
	 *	@var	array	�ȥ�󥶥��������������å�
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
		$this->db = null;
		$this->logger =& $controller->getLogger();
		$this->sql =& $controller->getSQL();
	}

	/**
	 *	DB����³����
	 *
	 *	@access	public
	 *	@return	mixed	0:���ｪλ Ethna_Error:���顼
	 */
	function connect()
	{
		$this->db =& DB::connect($this->dsn, $this->persistent);
		if (DB::isError($this->db)) {
			$error = Ethna::raiseError(E_DB_CONNECT, 'DB��³���顼: %s', $this->db->getUserInfo());
			$error->set('obj', $this->db);
			$this->db = null;
			return $error;
		}

		return 0;
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
		if (is_null($this->db)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 *	�������ȯ�Ԥ���
	 *
	 *	@access	public
	 *	@param	string	$query	SQLʸ
	 *	@return	mixed	DB_Result:��̥��֥������� Ethna_Error:���顼
	 */
	function &query($query)
	{
		return $this->_query($query);
	}

	/**
	 *	SQLʸ���ꥯ�����ȯ�Ԥ���
	 *
	 *	@access	public
	 *	@param	string	$sqlid		SQL-ID(+����)
	 *	@return	mixed	DB_Result:��̥��֥������� Ethna_Error:���顼
	 */
	function &sqlquery($sqlid)
	{
		$args = func_get_args();
		array_shift($args);
		$query = $this->sql->get($sqlid, $args);

		return $this->_query($query);
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
	 *	ľ���INSERT�ˤ��ID���������
	 *
	 *	@access	public
	 *	@return	int		ľ���INSERT�ˤ���������줿ID
	 *	@todo	MySQL�ʳ��б�
	 */
	function getInsertId()
	{
		return mysql_insert_id($this->db->connection);
	}

	/**
	 *	ľ��Υ�����ˤ�빹���Կ����������
	 *
	 *	@access	public
	 *	@return	int		�����Կ�
	 */
	function affectedRows()
	{
		return $this->db->affectedRows();
	}

	/**
	 *	�ơ��֥���å�����
	 *
	 *	@access	public
	 *	@param	mixed	��å��оݥơ��֥�̾
	 *	@return	mixed	DB_Result:��̥��֥������� Ethna_Error:���顼
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
	 *	@return	mixed	DB_Result:��̥��֥������� Ethna_Error:���顼
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
	 *	@return	mixed	0:���ｪλ Ethna_Error:���顼
	 */
	function begin()
	{
		if (count($this->transaction) > 0) {
			$this->transaction[] = true;
			return 0;
		}

		$r = $this->query('BEGIN;');
		if (Ethna::isError($r)) {
			return $r;
		}
		$this->transaction[] = true;

		return 0;
	}

	/**
	 *	DB�ȥ�󥶥����������Ǥ���
	 *
	 *	@access	public
	 *	@return	mixed	0:���ｪλ Ethna_Error:���顼
	 */
	function rollback()
	{
		if (count($this->transaction) == 0) {
			return 0;
		}

		// ����Хå����ϥ����å����˴ؤ�餺�ȥ�󥶥������򥯥ꥢ����
		$r = $this->query('ROLLBACK;');
		if (Ethna::isError($r)) {
			return $r;
		}
		$this->transaction = array();

		return 0;
	}

	/**
	 *	DB�ȥ�󥶥�������λ����
	 *
	 *	@access	public
	 *	@return	mixed	0:���ｪλ Ethna_Error:���顼
	 */
	function commit()
	{
		if (count($this->transaction) == 0) {
			return 0;
		} else if (count($this->transaction) > 1) {
			array_pop($this->transaction);
			return 0;
		}

		$r = $this->query('COMMIT;');
		if (Ethna::isError($r)) {
			return $r;
		}
		array_pop($this->transaction);

		return 0;
	}

	/**
	 *	�������ȯ�Ԥ���
	 *
	 *	@access	private
	 *	@param	string	$query	SQLʸ
	 *	@return	mixed	DB_Result:��̥��֥������� Ethna_Error:���顼
	 */
	function &_query($query)
	{
		$this->logger->log(LOG_DEBUG, "$query");
		$r =& $this->db->query($query);
		if (DB::isError($r)) {
			if ($r->getCode() == DB_ERROR_ALREADY_EXISTS) {
				$error = Ethna::raiseNotice(E_DB_DUPENT, '��ˡ������󥨥顼[%s]', $query);
				$error->set('obj', $r);
				return $error;
			} else {
				$error = Ethna::raiseError(E_DB_QUERY, '�����ꥨ�顼[%s]', $query);
				$error->set('obj', $r);
				return $error;
			}
		}
		return $r;
	}
}
// }}}
?>

<?php
/**
 *	app_object.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

/** ���ץꥱ������󥪥֥������Ⱦ���: ���Ѳ�ǽ */
define('OBJECT_STATE_ACTIVE', 0);
/** ���ץꥱ������󥪥֥������Ⱦ���: �����Բ� */
define('OBJECT_STATE_INACTIVE', 100);


/** ���ץꥱ������󥪥֥������ȥ����ȥե饰: ���� */
define('OBJECT_SORT_ASC', 0);
/** ���ץꥱ������󥪥֥������ȥ����ȥե饰: �߽� */
define('OBJECT_SORT_DESC', 1);


/** ���ץꥱ������󥪥֥������ȸ������: != */
define('OBJECT_CONDITION_NE', 0);

/** ���ץꥱ������󥪥֥������ȸ������: == */
define('OBJECT_CONDITION_EQ', 1);

/** ���ץꥱ������󥪥֥������ȸ������: LIKE */
define('OBJECT_CONDITION_LIKE', 2);

/** ���ץꥱ������󥪥֥������ȸ������: > */
define('OBJECT_CONDITION_GT', 3);

/** ���ץꥱ������󥪥֥������ȸ������: < */
define('OBJECT_CONDITION_LT', 4);

/** ���ץꥱ������󥪥֥������ȸ������: >= */
define('OBJECT_CONDITION_GE', 5);

/** ���ץꥱ������󥪥֥������ȸ������: <= */
define('OBJECT_CONDITION_LE', 6);


/** ���ץꥱ������󥪥֥������ȥ���ݡ��ȥ��ץ����: NULL�ץ�ѥƥ�̵�Ѵ� */
define('OBJECT_IMPORT_IGNORE_NULL', 1);

/** ���ץꥱ������󥪥֥������ȥ���ݡ��ȥ��ץ����: NULL�ץ�ѥƥ�����ʸ�����Ѵ� */
define('OBJECT_IMPORT_CONVERT_NULL', 2);


/**
 *	���ץꥱ�������ޥ͡�����Υ١������饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_AppManager
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	object	Ethna_Backend		backend���֥�������
	 */
	var $backend;

	/**
	 *	@var	object	Ethna_Config		���ꥪ�֥�������
	 */
	var $config;

	/**
	 *  @var    object  Ethna_DB      DB���֥�������
	 */
	var $db;

	/**
	 *	@var	object	Ethna_I18N			i18n���֥�������
	 */
	var $i18n;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������
	 */
	var $action_error;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������(��ά��)
	 */
	var $ae;

	/**
	 *	@var	object	Ethna_ActionForm	action form���֥�������
	 */
	var $action_form;

	/**
	 *	@var	object	Ethna_ActionForm	action form���֥�������(��ά��)
	 */
	var $af;

	/**
	 *	@var	object	Ethna_Session		���å���󥪥֥�������
	 */
	var $session;

	/**#@-*/

	/**
	 *	Ethna_AppManager�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	object	Ethna_Backend	&$backend	backend���֥�������
	 */
	function Ethna_AppManager(&$backend)
	{
		// ���ܥ��֥������Ȥ�����
		$this->backend =& $backend;
		$this->config = $backend->getConfig();
		$this->i18n =& $backend->getI18N();
		$this->action_error =& $backend->getActionError();
		$this->ae =& $this->action_error;
		$this->action_form =& $backend->getActionForm();
		$this->af =& $this->action_form;
		$this->session =& $backend->getSession();
		$this->db =& $this->backend->getDB();
	}

	/**
	 *	°���ΰ������֤�
	 *
	 *	@access	public
	 *	@param	string	$attr_name	°����̾��(�ѿ�̾)
	 *	@return	array	°���Ͱ���
	 */
	function getAttrList($attr_name)
	{
		$varname = $attr_name . "_list";
		return $this->$varname;
	}

	/**
	 *	°����ɽ��̾���֤�
	 *
	 *	@access	public
	 *	@param	string	$attr_name	°����̾��(�ѿ�̾)
	 *	@param	mixed	$id			°��ID
	 *	@return	string	°����ɽ��̾
	 */
	function getAttrName($attr_name, $id)
	{
		$varname = $attr_name . "_list";
		if (is_array($this->$varname) == false) {
			return null;
		}
		if (isset($this->$varname[$id]['name']) == false) {
			return null;
		}
		return $this->$varname[$id]['name'];
	}

	/**
	 *	°����ɽ��̾(�ܺ�)���֤�
	 *
	 *	@access	public
	 *	@param	string	$attr_name	°����̾��(�ѿ�̾)
	 *	@param	mixed	$id			°��ID
	 *	@return	string	°���ξܺ�ɽ��̾
	 */
	function getAttrLongName($attr_name, $id)
	{
		$varname = $attr_name . "_list";
		if (is_array($this->$varname) == false) {
			return null;
		}
		if (isset($this->$varname[$id]['long_name']) == false) {
			return null;
		}
		return $this->$varname[$id]['long_name'];
	}
}

/**
 *	���ץꥱ������󥪥֥������ȤΥ١������饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 *	@todo		ʣ���ơ��֥��JOIN�б�
 *	@todo		ʣ�������ˤ��ץ饤�ޥꥭ�����ѻ���ư���
 */
class Ethna_AppObject
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	object	Ethna_Backend		backend���֥�������
	 */
	var $backend;

	/**
	 *	@var	object	Ethna_Config		���ꥪ�֥�������
	 */
	var $config;

	/**
	 *  @var    object  Ethna_DB      DB���֥�������
	 */
	var $db;

	/**
	 *	@var	object	Ethna_I18N			i18n���֥�������
	 */
	var $i18n;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������
	 */
	var $action_error;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������(��ά��)
	 */
	var $ae;

	/**
	 *	@var	object	Ethna_ActionForm	action form���֥�������
	 */
	var $action_form;

	/**
	 *	@var	object	Ethna_ActionForm	action form���֥�������(��ά��)
	 */
	var $af;

	/**
	 *	@var	object	Ethna_Session		���å���󥪥֥�������
	 */
	var $session;

	/**
	 *	@var	array	�ơ��֥����
	 */
	var	$table_def = array();

	/**
	 *	@var	array	�ץ�ѥƥ����
	 */
	var	$prop_def = array();

	/**
	 *	@var	array	�ץ�ѥƥ�
	 */
	var	$prop = null;
	var $prop_backup = null;

	/** 
	 *	@var	array	�ץ饤�ޥꥭ�����
	 */
	var	$id_def = null;

	/**
	 *	@var	int		���֥�������ID
	 */
	var	$id = null;

	/**#@-*/


	/**
	 *	Ethna_AppObject���饹�Υ��󥹥ȥ饯��
	 */
	function Ethna_AppObject(&$backend, $key_type = null, $key = null)
	{
		$this->backend =& $backend;
		$this->config =& $backend->getConfig();
		$this->db =& $backend->getDB();
		$this->action_error =& $backend->getActionError();
		$this->ae =& $this->action_error;
		$this->action_form =& $backend->getActionForm();
		$this->af =& $this->action_form;
		$this->session =& $backend->getSession();

		$c =& $backend->getController();

		// Ethna_AppManager���֥������Ȥ�����
		$manager_list = $c->getManagerList();
		foreach ($manager_list as $k => $v) {
			$this->$k = $backend->getManager($v);
		}

		// ���֥������ȤΥץ饤�ޥꥭ���������
		foreach ($this->prop_def as $k => $v) {
			if ($v['primary'] == false) {
				continue;
			}
			if (is_null($this->id_def)) {
				$this->id_def = $k;
			} else if (is_array($this->id_def)) {
				$this->id_def[] = $k;
			} else {
				$this->id_def = array($this->id_def, $k);
			}
		}

		// ���������������å�
		if (is_null($key_type) && is_null($key)) {
			// perhaps for adding object
			return;
		}
		$key_type = to_array($key_type);
		$key = to_array($key);
		if (count($key_type) != count($key)) {
			trigger_error(sprintf("Unmatched key_type & key length [%d-%d]", count($key_type), count($key)), E_USER_ERROR);
			return;
		}
		foreach ($key_type as $elt) {
			if (isset($this->prop_def[$elt]) == false) {
				trigger_error("Invalid key_type [$elt]", E_USER_ERROR);
				return;
			}
		}

		// SQLʸ����
		$sql = $this->_getSQL_Select($key_type, $key);

		// �ץ�ѥƥ�����
		$r =& $this->db->query($sql);
		if ($r == null) {
			return;
		}
		$n = $r->numRows();
		if ($n == 0) {
			// try default
			if ($this->_setDefault($key_type, $key) == false) {
				// nop
			}
			return;
		} else if ($n > 1) {
			trigger_error("Invalid key (multiple rows found) [$key]", E_USER_ERROR);
			return;
		}
		$this->prop = $r->fetchRow(DB_FETCHMODE_ASSOC);
		$this->prop_backup = $this->prop;

		if (is_array($this->id_def)) {
			$this->id = array();
			foreach ($this->id_def as $k) {
				$this->id[] = $this->prop[$k];
			}
		} else {
			$this->id = $this->prop[$this->id_def];
		}
	}

	/**
	 *	ͭ���ʥ��֥������Ȥ��ɤ������֤�
	 *
	 *	@access	public
	 *	@return	bool	true:ͭ�� false:̵��
	 */
	function isValid()
	{
		return is_null($this->id) ? false : true;
	}

	/**
	 *	�����ƥ��֤ʥ��֥������Ȥ��ɤ������֤�
	 *
	 *	isValid()�᥽�åɤϥ��֥������ȼ��Τ�ͭ�����ɤ�����Ƚ�ꤹ��Τ��Ф�
	 *	isActive()�ϥ��֥������Ȥ����ץꥱ�������Ȥ���ͭ�����ɤ������֤�
	 *
	 *	@access	public
	 *	@return	bool	true:�����ƥ��� false:�󥢥��ƥ���
	 */
	function isActive()
	{
		if ($this->isValid() == false) {
			return false;
		}
		return $this->prop['state'] == OBJECT_STATE_ACTIVE ? true : false;
	}

	/**
	 *	���֥������ȤΥץ�ѥƥ�������֤�
	 *
	 *	@access	public
	 *	@return	array	���֥������ȤΥץ�ѥƥ����
	 */
	function getDef()
	{
		return $this->prop_def;
	}

	/**
	 *	�ץ饤�ޥꥭ��������֤�
	 *
	 *	@access	public
	 *	@return	mixed	�ץ饤�ޥꥭ���Ȥʤ�ץ�ѥƥ�̾
	 */
	function getIdDef()
	{
		return $this->id_def;
	}

	/**
	 *	���֥�������ID���֤�
	 *
	 *	@access	public
	 *	@return	mixed	���֥�������ID
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 *	���֥������ȥץ�ѥƥ��ؤΥ�������(R)
	 *
	 *	@access	public
	 *	@param	string	$key	�ץ�ѥƥ�̾
	 *	@return	mixed	�ץ�ѥƥ�
	 */
	function get($key)
	{
		if (isset($this->prop_def[$key]) == false) {
			trigger_error(sprintf("Unknown property [%s]", $key), E_USER_ERROR);
			return null;
		}
		if (isset($this->prop[$key])) {
			return $this->prop[$key];
		}
		return null;
	}

	/**
	 *	���֥������ȥץ�ѥƥ�ɽ��̾�ؤΥ�������
	 *
	 *	@access	public
	 *	@param	string	$key	�ץ�ѥƥ�̾
	 *	@return	string	�ץ�ѥƥ���ɽ��̾
	 */
	function getName($key)
	{
		return $this->get($key);
	}

	/**
	 *	���֥������ȥץ�ѥƥ�ɽ��̾(�ܺ�)�ؤΥ�������
	 *
	 *	@access	public
	 *	@param	string	$key	�ץ�ѥƥ�̾
	 *	@return	string	�ץ�ѥƥ���ɽ��̾(�ܺ�)
	 */
	function getLongName($key)
	{
		return $this->get($key);
	}

	/**
	 *	�ץ�ѥƥ�ɽ��̾���Ǽ����Ϣ��������������
	 *
	 *	@access	public
	 *	@return	array	�ץ�ѥƥ�ɽ��̾���Ǽ����Ϣ������
	 */
	function getNameObject()
	{
		$object = array();

		foreach ($this->prop_def as $key => $elt) {
			$object[$elt['form_name']] = $this->getName($key);
		}

		return $object;
	}

	/**
	 *	���֥������ȥץ�ѥƥ��ؤΥ�������(W)
	 *
	 *	@access	public
	 *	@param	string	$key	�ץ�ѥƥ�̾
	 *	@param	string	$value	�ץ�ѥƥ���
	 */
	function set($key, $value)
	{
		if (isset($this->prop_def[$key]) == false) {
			trigger_error(sprintf("Unknown property [%s]", $key), E_USER_ERROR);
			return null;
		}
		$this->prop[$key] = $value;
	}

	/**
	 *	���֥������ȥץ�ѥƥ������η����ǥ���פ���(���ߤ�CSV�����Τߥ��ݡ���)
	 *
	 *	@access	public
	 *	@param	string	$type	����׷���("csv"...)
	 *	@return	string	����׷��(���顼�ξ���null)
	 */
	function dump($type = "csv")
	{
		$method = "_dump_$type";
		if (method_exists($this, $method) == false) {
			return null;
		}

		return $this->$method();
	}

	/**
	 *	�ե������ͤ��饪�֥������ȥץ�ѥƥ��򥤥�ݡ��Ȥ���
	 *
	 *	@access	public
	 *	@param	int		$option	����ݡ��ȥ��ץ����(OBJECT_IMPORT_IGNORE_NULL,...)
	 */
	function importForm($option = null)
	{
		foreach ($this->getDef() as $k => $def) {
			$value = $this->af->get($def['form_name']);
			if (is_null($value)) {
				// �ե����फ���ͤ���������Ƥ��ʤ����ο���
				if ($option == OBJECT_IMPORT_IGNORE_NULL) {
					// null�ϥ����å�
					continue;
				} else if ($option == OBJECT_IMPORT_CONVERT_NULL) {
					// ��ʸ������Ѵ�
					$value = '';
				}
			}
			$this->set($k, $value);
		}
	}

	/**
	 *	���֥������ȥץ�ѥƥ���ե������ͤ˥������ݡ��Ȥ���
	 *
	 *	@access	public
	 */
	function exportForm()
	{
		foreach ($this->getDef() as $k => $def) {
			$this->af->set($def['form_name'], $this->get($k));
		}
	}

	/**
	 *	���֥������Ȥ��ɲä���
	 *
	 *	@access	public
	 *	@return	bool	true:���ｪλ false:���顼
	 */
	function add()
	{
		// ��ˡ��������å�(DB����Υ��顼�ǤϤɤΥ���ब��ʣ��������Ƚ��Ǥ��ʤ�)
		$duplicate_key_list = $this->_getDuplicateKeyList();
		if (is_array($duplicate_key_list) && count($duplicate_key_list) > 0) {
			foreach ($duplicate_key_list as $k) {
				$this->action_error->add(E_APP_DUPOBJ, null, '%s is already used', $k);
			}
			return false;
		} else if ($duplicate_key_list === false) {
			return false;
		}

		$sql = $this->_getSQL_Add();
		$r =& $this->db->query_test($sql);
		if (DB::isError($r)) {
			if ($r->getCode() == DB_ERROR_ALREADY_EXISTS) {
				// �졼������ǥ������
				$this->action_error->add(E_APP_DUPOBJ, null, 'duplicate key');
			} else {
				// ����¾��̿Ū���顼
				trigger_error(sprintf("db error: %s [%s]", $r->getMessage(), $query), E_USER_ERROR);
			}
			return false;
		}

		$this->prop_backup = $this->prop;

		// ���֥�������ID�μ���
		$insert_id = false;
		foreach (to_array($this->id_def) as $k) {
			if (isset($this->prop[$k]) == false || $this->prop[$k] === "" || $this->prop[$k] === null) {
				$insert_id = true;
				break;
			}
		}
		if ($insert_id) {
			$this->id = $this->db->getInsertId();
		} else {
			if (is_array($this->id_def)) {
				$this->id = array();
				foreach ($this->id_def as $k) {
					$this->id[] = $this->prop[$k];
				}
			} else {
				$this->id = $this->prop[$this->id_def];
			}
		}
		return $this->id;
	}

	/**
	 *	���֥������Ȥ򹹿�����
	 *
	 *	@access	public
	 *	@return	bool	true:���ｪλ false:���顼
	 */
	function update()
	{
		// ��ˡ��������å�(DB����Υ��顼�ǤϤɤΥ���ब��ʣ��������Ƚ��Ǥ��ʤ�)
		$duplicate_key_list = $this->_getDuplicateKeyList();
		if (is_array($duplicate_key_list) && count($duplicate_key_list) > 0) {
			foreach ($duplicate_key_list as $k) {
				$this->action_error->add(E_APP_DUPOBJ, null, '%s is already used', $k);
			}
			return false;
		} else if ($duplicate_key_list === false) {
			return false;
		}

		$sql = $this->_getSQL_Update();
		$r =& $this->db->query_test($sql);
		if (DB::isError($r)) {
			if ($r->getCode() == DB_ERROR_ALREADY_EXISTS) {
				// �졼������ǥ������
				$this->action_error->add(E_APP_DUPOBJ, null, 'duplicate key');
			} else {
				// ����¾��̿Ū���顼
				trigger_error(sprintf("db error: %s [%s]", $r->getMessage(), $query), E_USER_ERROR);
			}
			return false;
		}

		$this->prop_backup = $this->prop;

		return true;
	}

	/**
	 *	���֥������Ȥ�������
	 *
	 *	@access	public
	 *	@return	bool	true:���ｪλ false:���顼
	 */
	function remove()
	{
		$sql = $this->_getSQL_Remove();
		$r =& $this->db->query($sql);
		if ($r == null) {
			return false;
		}

		$this->id = $this->prop = $this->prop_backup = null;

		return true;
	}

	/**
	 *	���֥������Ȥ򸡺�����
	 *
	 *	@access	public
	 *	@param	array	$filter		�������
	 *	@param	array	$order		������̥����Ⱦ��
	 *	@param	int		$offset		������̼������ե��å�
	 *	@param	int		$count		������̼�����
	 *	@return	array	0 => �������˥ޥå��������, 1 => $offset, $count�ˤ����ꤵ�줿����Υ��֥�������ID����
	 */
	function search($filter = null, $order = null, $offset = null, $count = null)
	{
		if (is_null($filter) == false) {
			$sql = $this->_getSQL_SearchLength($filter);
			$r =& $this->db->query($sql);
			if ($r == null) {
				return false;
			}
			$row = $r->fetchRow(DB_FETCHMODE_ASSOC);
			$length = $row['id_count'];
		} else {
			$length = null;
		}

		$id_list = array();
		$sql = $this->_getSQL_Search($filter, $order, $offset, $count);
		$r =& $this->db->query($sql);
		if ($r == null) {
			return false;
		}
		$n = $r->numRows();
		for ($i = 0; $i < $n; $i++) {
			$row = $r->fetchRow(DB_FETCHMODE_ASSOC);

			// �ץ饤�ޥꥭ����1�����ʤ饹���顼�ͤ��Ѵ�
			if (is_array($this->id_def) == false) {
				$row = $row[$this->id_def];
			}
			$id_list[] = $row;
		}
		if (is_null($length)) {
			$length = count($id_list);
		}

		return array($length, $id_list);
	}

	/**
	 *	���֥������ȤΥ��ץꥱ�������ǥե���ȥץ�ѥƥ������ꤹ��
	 *
	 *	���󥹥ȥ饯���ˤ����ꤵ�줿�����˥ޥå����륨��ȥ꤬�ʤ��ä�����
	 *	�ǥե���ȥץ�ѥƥ��򤳤������ꤹ�뤳�Ȥ������
	 *
	 *	@access	protected
	 *	@return	bool	true:�ǥե���ȥץ�ѥƥ������� false:�����򥹥��å�
	 */
	function _setDefault($key_type, $key)
	{
		return false;
	}

	/**
	 *	���֥������ȤΥץ饤�ޥ�ơ��֥���������
	 *
	 *	@access	private
	 *	@return	string	���֥������ȤΥץ饤�ޥ�ơ��֥�̾
	 */
	function _getPrimaryTable()
	{
		$tables = array_keys($this->table_def);
		$table = $tables[0];
		
		return $table;
	}

	/**
	 *	��ʣ�������������
	 *
	 *	@access	private
	 *	@return	mixed	true:��ʣ�ʤ� false:���顼 array:��ʣ�����Υץ�ѥƥ�̾����
	 */
	function _getDuplicateKeyList()
	{
		$duplicate_key_list = array();

		// �������ꤵ��Ƥ���ץ饤�ޥꥭ����NULL���ޤޤ����ϸ������ʤ�
		$check_pkey = true;
		foreach (to_array($this->id_def) as $k) {
			if (isset($this->prop[$k]) == false || is_null($this->prop[$k])) {
				$check_pkey = false;
				break;
			}
		}

		// �ץ饤�ޥꥭ����multi columns�ˤʤ�����Τ��̰���
		if ($check_pkey) {
			$sql = $this->_getSQL_Duplicate($this->id_def);
			$r =& $this->db->query($sql);
			if ($r == null) {
				return false;
			} else if ($r->numRows() > 0) {
				$duplicate_key_list = to_array($this->id_def); // we can overwrite $key_list here
			}
		}

		// ��ˡ�������
		foreach ($this->prop_def as $k => $v) {
			if ($v['primary'] == true || $v['key'] == false) {
				continue;
			}
			$sql = $this->_getSQL_Duplicate($k);
			$r =& $this->db->query($sql);
			if ($r == null) {
				$this->action_error->add(E_DB_QUERY, null, $r->getMessage());
				return false;
			} else if ($r->NumRows() > 0) {
				$duplicate_key_list[] = $k;
			}
		}

		if (count($duplicate_key_list) > 0) {
			return $duplicate_key_list;
		} else {
			return true;
		}
	}

	/**
	 *	���֥������ȥץ�ѥƥ����������SQLʸ���ۤ���
	 *
	 *	@access	private
	 *	@param	array	$key_type	�����Ȥʤ�ץ�ѥƥ�̾����
	 *	@param	array	$key		$key_type���б����륭������
	 *	@return	string	SELECTʸ
	 */
	function _getSQL_Select($key_type, $key)
	{
		// SQL����������
		Ethna_AppSQL::escapeSQL($key);

		$tables = implode(',', array_keys($this->table_def));
		$columns = implode(',', array_keys($this->prop_def));

		// �������
		$condition = null;
		for ($i = 0; $i < count($key_type); $i++) {
			if (is_null($condition)) {
				$condition = "WHERE ";
			} else {
				$condition = " AND ";
			}
			$elt = $this->prop_def[$key_type[$i]];
			$condition .= Ethna_AppSQL::getCondition($key_type[$i], $key);
		}

		$sql = "SELECT $columns FROM $tables $condition;";

		return $sql;
	}

	/**
	 *	���֥������Ȥ��ɲä���SQLʸ���ۤ���
	 *
	 *	@access	private
	 *	@return	string	���֥������Ȥ��ɲä��뤿���INSERTʸ
	 */
	function _getSQL_Add()
	{
		$tables = implode(',', array_keys($this->table_def));

		// SET�繽��
		$set_list = "";
		$prop_arg_list = $this->prop;
		Ethna_AppSQL::escapeSQL($prop_arg_list);
		foreach ($this->prop_def as $k => $v) {
			if (isset($prop_arg_list[$k]) == false) {
				continue;
			}
			if ($set_list != "") {
				$set_list .= ",";
			}
			$set_list .= sprintf("%s=%s", $k, $prop_arg_list[$k]);
		}

		$sql = "INSERT INTO $tables SET $set_list;";

		return $sql;
	}

	/**
	 *	���֥������ȥץ�ѥƥ��򹹿�����SQLʸ���ۤ���
	 *
	 *	@access	private
	 *	@return	���֥������ȥץ�ѥƥ��򹹿����뤿���UPDATEʸ
	 */
	function _getSQL_Update()
	{
		$tables = implode(',', array_keys($this->table_def));

		// SET�繽��
		$set_list = "";
		$prop_arg_list = $this->prop;
		Ethna_AppSQL::escapeSQL($prop_arg_list);
		foreach ($this->prop_def as $k => $v) {
			if ($set_list != "") {
				$set_list .= ",";
			}
			$set_list .= sprintf("%s=%s", $k, $prop_arg_list[$k]);
		}

		// �������(primary key)
		$condition = null;
		foreach (to_array($this->id_def) as $k) {
			if (is_null($condition)) {
				$condition = "WHERE ";
			} else {
				$condition = " AND ";
			}
			$v = $this->prop_backup[$k];	// equals to $this->id
			Ethna_AppSQL::escapeSQL($v);
			$condition .= Ethna_AppSQL::getCondition($k, $v);
		}

		$sql = "UPDATE $tables SET $set_list $condition;";

		return $sql;
	}

	/**
	 *	���֥������Ȥ�������SQLʸ���ۤ���
	 *
	 *	@access	private
	 *	@return	string	���֥������Ȥ������뤿���DELETEʸ
	 */
	function _getSQL_Remove()
	{
		$tables = implode(',', array_keys($this->table_def));

		// �������(primary key)
		$condition = null;
		foreach (to_array($this->id_def) as $k) {
			if (is_null($condition)) {
				$condition = "WHERE ";
			} else {
				$condition = " AND ";
			}
			$v = $this->prop_backup[$k];	// equals to $this->id
			Ethna_AppSQL::escapeSQL($v);
			$condition .= Ethna_AppSQL::getCondition($k, $v);
		}
		if (is_null($condition)) {
			trigger_error("DELETE with no conditon", E_USER_ERROR);
			return false;
		}

		$sql = "DELETE FROM $tables $condition;";

		return $sql;
	}

	/**
	 *	���֥������ȥץ�ѥƥ��Υ�ˡ��������å���Ԥ�SQLʸ���ۤ���
	 *
	 *	@access	private
	 *	@param	mixed	$key	��ˡ��������å���Ԥ��ץ�ѥƥ�̾
	 *	@return	string	��ˡ��������å���Ԥ������SELECTʸ
	 */
	function _getSQL_Duplicate($key)
	{
		$tables = implode(',', array_keys($this->table_def));
		$columns = implode(',', array_keys($this->prop_def));	// any column will do

		$condition = null;
		// �������(�������ꤵ��Ƥ���ץ饤�ޥꥭ���ϸ����оݤ������)
		if (is_null($this->id) == false) {
			foreach (to_array($this->id_def) as $k) {
				if (is_null($condition)) {
					$condition = "WHERE ";
				} else {
					$condition .= " AND ";
				}
				$v = $this->prop_backup[$k];	// equals to $this->id
				Ethna_AppSQL::escapeSQL($v);
				$condition .= Ethna_AppSQL::getCondition($k, $v, OBJECT_CONDITION_NE);
			}
		}

		foreach (to_array($key) as $k) {
			if (is_null($condition)) {
				$condition = "WHERE ";
			} else {
				$condition .= " AND ";
			}
			$v = $this->prop[$k];
			Ethna_AppSQL::escapeSQL($v);
			$condition .= Ethna_AppSQL::getCondition($k, $v);
		}

		$sql = "SELECT $columns FROM $tables $condition;";

		return $sql;
	}

	/**
	 *	���֥������ȸ������(offset, count����)���������SQLʸ���ۤ���
	 *
	 *	@access	private
	 *	@param	array	$filter		�������
	 *	@return	string	���������������뤿���SELECTʸ
	 */
	function _getSQL_SearchLength($filter)
	{
		$tables = implode(',', array_keys($this->table_def));

		// �������ɲåơ��֥�JOIN
		$tables .= $this->_SQLPlugin_SearchTable();

		$id_def = to_array($this->id_def);
		$column_id = $this->_getPrimaryTable() . "." . $id_def[0];	// any id columns will do

		$condition = $this->_getSQL_SearchCondition($filter);
		$sql = "SELECT DISTINCT COUNT($column_id) AS id_count FROM $tables $condition;";

		return $sql;
	}

	/**
	 *	���֥������ȸ�����Ԥ�SQLʸ���ۤ���
	 *
	 *	@access	private
	 *	@param	array	$filter		�������
	 *	@param	array	$order		������̥����Ⱦ��
	 *	@param	int		$offset		������̼������ե��å�
	 *	@param	int		$count		������̼�����
	 *	@return	string	���֥������ȸ�����Ԥ�SELECTʸ
	 */
	function _getSQL_Search($filter, $order, $offset, $count)
	{
		$tables = implode(',', array_keys($this->table_def));

		// �������ɲåơ��֥�JOIN
		$tables .= $this->_SQLPlugin_SearchTable();

		$column_id = "";
		foreach (to_array($this->id_def) as $id) {
			if ($column_id != "") {
				$column_id .= ",";
			}
			$column_id .= $this->_getPrimaryTable() . "." . $id;
		}
		$condition = $this->_getSQL_SearchCondition($filter);

		$sort = "";
		if (is_array($order)) {
			foreach ($order as $k => $v) {
				if ($sort == "") {
					$sort = "ORDER BY ";
				} else {
					$sort .= ", ";
				}
				$sort .= sprintf("%s %s", $k, $v == OBJECT_SORT_ASC ? "ASC" : "DESC");
			}
		}

		$limit = "";
		if (is_null($count) == false) {
			$limit = "LIMIT ";
			if (is_null($offset) == false) {
				$limit .= sprintf("%d,", $offset);
			}
			$limit .= sprintf("%d", $count);
		}

		$sql = "SELECT DISTINCT $column_id FROM $tables $condition $sort $limit;";

		return $sql;
	}

	/**
	 *	���֥������ȸ���SQL�ξ��ʸ���ۤ���
	 *
	 *	@access	private
	 *	@param	array	$filter		�������
	 *	@return	string	���֥������ȸ����ξ��ʸ(���顼�ʤ�null)
	 */
	function _getSQL_SearchCondition($filter)
	{
		if (is_array($filter) == false) {
			return "";
		}

		$p_table = $this->_getPrimaryTable();

		// �������ɲåץ�ѥƥ�
		$search_prop_def = $this->_SQLPlugin_SearchPropDef();

		$prop_def = array_merge($this->prop_def, $search_prop_def);

		$condition = null;
		foreach ($filter as $k => $v) {
			if (isset($prop_def[$k]) == false) {
				trigger_error(sprintf("Unknown property [%s]", $k), E_USER_ERROR);
				return null;
			}

			if (is_null($condition)) {
				$condition = "WHERE ";
			} else {
				$condition .= " AND ";
			}

			$t = isset($prop_def[$k]['table']) ? $prop_def[$k]['table'] : $p_table;

			if (is_object($v)) {
				// Ethna_AppSearchObject�����ꤵ��Ƥ�����
				$tmp = $v->value;
				Ethna_AppSQL::escapeSQL($tmp);
				$condition .= Ethna_AppSQL::getCondition("$t.$k", $tmp, $v->condition);
			} else if (is_array($v) && count($v) > 0 && is_object($v[0])) {
				// Ethna_AppSearchObject������ǻ��ꤵ��Ƥ�����
				$n = 0;
				foreach ($v as $so) {
					if ($n > 0) {
						$condition .= " AND ";
					}
					$tmp = $so->value;
					Ethna_AppSQL::escapeSQL($tmp);
					$condition .= Ethna_AppSQL::getCondition("$t.$k", $tmp, $so->condition);
					$n++;
				}
			} else if ($prop_def[$k]['type'] == VAR_TYPE_STRING) {
				// ��ά��(ʸ����)
				Ethna_AppSQL::escapeSQL($v);
				$condition .= Ethna_AppSQL::getCondition("$t.$k", $v, OBJECT_CONDITION_LIKE);
			} else {
				// ��ά��(����)
				Ethna_AppSQL::escapeSQL($v);
				$condition .= Ethna_AppSQL::getCondition("$t.$k", $v, OBJECT_CONDITION_EQ);
			}
		}

		return $condition;
	}

	/**
	 *	���֥������ȸ���SQL�ץ饰����(�ɲåơ��֥�)
	 *
	 *	sample:
	 *	<code>
	 *	return " LEFT JOIN bar_tbl ON foo_tbl.user_id=bar_tbl.user_id";
	 *	</code>
	 *
	 *	@access	protected
	 *	@return	string	�ơ��֥�JOIN��SQLʸ
	 */
	function _SQLPlugin_SearchTable()
	{
		return "";
	}

	/**
	 *	���֥������ȸ���SQL�ץ饰����(�ɲþ�����)
	 *
	 *	sample:
	 *	<code>
	 *	$search_prop_def = array(
	 *	  'group_id' => array(
	 *	    'primary' => true, 'key' => true, 'type' => VAR_TYPE_INT,
	 *	    'form_name' => 'group_id', 'table' => 'group_user_tbl',
	 *	  ),
	 *	);
	 *	return $search_prop_def;
	 *	</code>
	 *
	 *	@access	protected
	 *	@return	array	�ɲþ�����
	 */
	function _SQLPlugin_SearchPropDef()
	{
	}

	/**
	 *	���֥������ȥץ�ѥƥ���CSV�����ǥ���פ���
	 *
	 *	@access	protected
	 *	@return	string	����׷��
	 */
	function _dump_csv()
	{
		$dump = "";

		$n = 0;
		foreach ($this->getDef() as $k => $def) {
			if ($n > 0) {
				$dump .= ",";
			}
			$dump .= Ethna_Util::escapeCSV($this->getName($k));
			$n++;
		}

		return $dump;
	}
}

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

	/**
	 *	@var	string	������
	 */
	var $value;

	/**
	 *	@var	int		�������
	 */
	var $condition;

	/**#@-*/


	/**
	 *	Ethna_AppSearchObject�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	string	$value		������
	 *	@param	int		$condition	�������(OBJECT_CONDITION_NE,...)
	 */
	function AppSearchObject($value, $condition)
	{
		$this->value = $value;
		$this->condition = $condition;
	}
}
?>

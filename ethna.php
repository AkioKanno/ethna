<?php
/**
 *	ethna.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

/**	Ethna��¸�饤�֥��: PEAR::DB */
include_once('DB.php');

/** Ethna��¸�饤�֥��: Smarty */
include_once('Smarty/Smarty.class.php');


/** Ethna����ݡ��ͥ��: Action Class */
include_once('ethna/class/action_class.php');

/** Ethna����ݡ��ͥ��: Action Error */
include_once('ethna/class/action_error.php');

/** Ethna����ݡ��ͥ��: Action Form */
include_once('ethna/class/action_form.php');

/** Ethna����ݡ��ͥ��: Application���֥������� */
include_once('ethna/class/app_object.php');

/** Ethna����ݡ��ͥ��: �������ɽ�����֥������� */
include_once('ethna/class/app_info.php');

/** Ethna����ݡ��ͥ��: SQL���֥������� */
include_once('ethna/class/app_sql.php');

/** Ethna����ݡ��ͥ��: Backend */
include_once('ethna/class/backend.php');

/** Ethna����ݡ��ͥ��: Config */
include_once('ethna/class/config.php');

/** Ethna����ݡ��ͥ��: Controller */
include_once('ethna/class/controller.php');

/** Ethna����ݡ��ͥ��: DB */
include_once('ethna/class/db.php');

/** Ethna����ݡ��ͥ��: i18n */
include_once('ethna/class/i18n.php');

/** Ethna����ݡ��ͥ��: Logger */
include_once('ethna/class/log.php');

/** Ethna����ݡ��ͥ��: MailSender */
include_once('ethna/class/mail.php');

/** Ethna����ݡ��ͥ��: Session */
include_once('ethna/class/session.php');

/** Ethna����ݡ��ͥ��: SkeltonGenerator */
include_once('ethna/class/skelton.php');

/** Ethna����ݡ��ͥ��: Smarty�ץ饰����ؿ� */
include_once('ethna/class/smarty_plugin.php');

/** Ethna����ݡ��ͥ��: �桼�ƥ���ƥ� */
include_once('ethna/class/util.php');

if (extension_loaded('soap')) {
	/** Ethna����ݡ��ͥ��: SOAP�����ȥ����� */
	include_once('ethna/class/soap_gateway.php');

	/** Ethna����ݡ��ͥ��: SOAP�桼�ƥ���ƥ� */
	include_once('ethna/class/soap_util.php');

	/** Ethna����ݡ��ͥ��: WSDL�������饹 */
	include_once('ethna/class/soap_wsdl.php');
}

/** �С��������� */
define('ETHNA_VERSION', '0.1.0');


/** ���饤����ȸ������: �Ѹ� */
define('LANG_EN', 'en');

/**	���饤����ȸ������: ���ܸ� */
define('LANG_JA', 'ja');


/**	���饤����ȥ�����: �����֥֥饦��(PC) */
define('CLIENT_TYPE_WWW', 1);

/**	���饤����ȥ�����: SOAP���饤����� */
define('CLIENT_TYPE_SOAP', 2);

/**	���饤����ȥ�����: Flash Player (with Flash Remoting) */
define('CLIENT_TYPE_AMF', 3);


/**	���Ƿ�: ���� */
define('VAR_TYPE_INT', 1);

/**	���Ƿ�: ��ư�������� */
define('VAR_TYPE_FLOAT', 1);

/**	���Ƿ�: ʸ���� */
define('VAR_TYPE_STRING', 2);

/**	���Ƿ�: ���� */
define('VAR_TYPE_DATETIME', 3);

/**	���Ƿ�: ������ */
define('VAR_TYPE_BOOLEAN', 4);

/**	���Ƿ�: �ե����� */
define('VAR_TYPE_FILE', 5);


/** �ե����෿: text */
define('FORM_TYPE_TEXT', 1);

/** �ե����෿: password */
define('FORM_TYPE_PASSWORD', 2);

/** �ե����෿: textarea */
define('FORM_TYPE_TEXTAREA', 3);

/** �ե����෿: select */
define('FORM_TYPE_SELECT', 4);

/** �ե����෿: radio */
define('FORM_TYPE_RADIO', 5);

/** �ե����෿: checkbox */
define('FORM_TYPE_CHECKBOX', 6);

/** �ե����෿: button */
define('FORM_TYPE_SUBMIT', 7);

/** �ե����෿: button */
define('FORM_TYPE_FILE', 8);


/**	���顼������: ���̥��顼 */
define('E_GENERAL', 1);

/**	���顼������: DB��³���顼 */
define('E_DB_CONNECT', 2);

/**	���顼������: DB����ʤ� */
define('E_DB_NODSN', 3);

/**	���顼������: DB�����ꥨ�顼 */
define('E_DB_QUERY', 4);

/**	���顼������: DB��ˡ����������顼 */
define('E_DB_DUPENT', 5);

/**	���顼������: ���å���󥨥顼(ͭ�������ڤ�) */
define('E_SESSION_EXPIRE', 16);

/**	���顼������: ���å���󥨥顼(IP���ɥ쥹�����å����顼) */
define('E_SESSION_IPCHECK', 17);

/**	���顼������: ���������̤������顼 */
define('E_APP_UNDEFINED_ACTION', 32);

/**	���顼������: ��������󥯥饹̤������顼 */
define('E_APP_UNDEFINED_ACTIONCLASS', 33);

/**	���顼������: ���ץꥱ������󥪥֥�������ID��ʣ���顼 */
define('E_APP_DUPENT', 34);

/** ���顼������: ���ץꥱ�������᥽�åɤ�¸�ߤ��ʤ� */
define('E_APP_NOMETHOD', 35);

/** ���顼������: ��å����顼 */
define('E_APP_LOCK', 36);

/** ���顼������: CSVʬ�䥨�顼(�Է�³) */
define('E_UTIL_CSV_CONTINUE', 64);

/**	���顼������: �ե������ͷ����顼(�����顼�������������) */
define('E_FORM_WRONGTYPE_SCALAR', 128);

/**	���顼������: �ե������ͷ����顼(��������˥����顼����) */
define('E_FORM_WRONGTYPE_ARRAY', 129);

/**	���顼������: �ե������ͷ����顼(������) */
define('E_FORM_WRONGTYPE_INT', 130);

/**	���顼������: �ե������ͷ����顼(��ư����������) */
define('E_FORM_WRONGTYPE_FLOAT', 131);

/**	���顼������: �ե������ͷ����顼(���շ�) */
define('E_FORM_WRONGTYPE_DATETIME', 132);

/**	���顼������: �ե������ͷ����顼(BOOL��) */
define('E_FORM_WRONGTYPE_BOOLEAN', 133);

/**	���顼������: �ե�������ɬ�ܥ��顼 */
define('E_FORM_REQUIRED', 134);

/**	���顼������: �ե������ͺǾ��ͥ��顼(������) */
define('E_FORM_MIN_INT', 135);

/**	���顼������: �ե������ͺǾ��ͥ��顼(��ư����������) */
define('E_FORM_MIN_FLOAT', 136);

/**	���顼������: �ե������ͺǾ��ͥ��顼(ʸ����) */
define('E_FORM_MIN_STRING', 137);

/**	���顼������: �ե������ͺǾ��ͥ��顼(���շ�) */
define('E_FORM_MIN_DATETIME', 138);

/**	���顼������: �ե������ͺǾ��ͥ��顼(�ե����뷿) */
define('E_FORM_MIN_FILE', 139);

/**	���顼������: �ե������ͺ����ͥ��顼(������) */
define('E_FORM_MAX_INT', 140);

/**	���顼������: �ե������ͺ����ͥ��顼(��ư����������) */
define('E_FORM_MAX_FLOAT', 141);

/**	���顼������: �ե������ͺ����ͥ��顼(ʸ����) */
define('E_FORM_MAX_STRING', 142);

/**	���顼������: �ե������ͺ����ͥ��顼(���շ�) */
define('E_FORM_MAX_DATETIME', 143);

/**	���顼������: �ե������ͺ����ͥ��顼(�ե����뷿) */
define('E_FORM_MAX_FILE', 144);

/**	���顼������: �ե�������ʸ����(����ɽ��)���顼 */
define('E_FORM_REGEXP', 145);

/**	���顼������: �ե������Ϳ���(������������å�)���顼 */
define('E_FORM_INVALIDVALUE', 146);

/**	���顼������: �ե�������ʸ����(������������å�)���顼 */
define('E_FORM_INVALIDCHAR', 147);

/**	���顼������: ��ǧ�ѥ���ȥ����ϥ��顼 */
define('E_FORM_CONFIRM', 148);


if (defined('E_STRICT') == false) {
	/**	PHP 5�Ȥθߴ��ݻ���� */
	define('E_STRICT', 0);
}


/**
 *	Ethna�ե졼�������쥯�饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna
{
	/**#@+
	 *	@access	private
	 */

	/**#@-*/

	/**
	 *	Ethna_Error���֥������Ȥ��ɤ�����Ƚ�ꤹ��(�ޤ��ϻ��ꤵ�줿���顼�����ɤ�
	 *	���顼���ɤ�����Ƚ�ꤹ��)
	 *
	 *	@access	public
	 *	@param	mixed	�᥽�åɶ��������
	 *	@param	int		���顼������
	 *	@return	bool	true:���顼 false:���ｪλ
	 *	@static
	 */
	function isError($obj, $code = null)
	{
		if (strcasecmp(get_class($obj), 'Ethna_Error') == 0 ||
			is_subclass_of($obj, 'Ethna_Error')) {
			if (is_null($code)) {
				return true;
			} else {
				return $obj->getCode() == $code;
			}
		}
		return false;
	}

	/**
	 *	Ethna_Error���֥������Ȥ���������(���顼��٥�:E_USER_ERROR)
	 *
	 *	@access	public
	 *	@param	int		$code				���顼������
	 *	@param	string	$message			���顼��å�����(+����)
	 *	@static
	 */
	function &raiseError($code, $message = null)
	{
		$message_arg_list = array_slice(func_get_args(), 2);
		return new Ethna_Error(E_USER_ERROR, $code, $message, $message_arg_list);
	}

	/**
	 *	Ethna_Error���֥������Ȥ���������(���顼��٥�:E_USER_WARNING)
	 *
	 *	@access	public
	 *	@param	int		$code				���顼������
	 *	@param	string	$message			���顼��å�����(+����)
	 *	@static
	 */
	function &raiseWarning($code, $message = null)
	{
		$message_arg_list = array_slice(func_get_args(), 2);
		return new Ethna_Error(E_USER_WARNING, $code, $message, $message_arg_list);
	}

	/**
	 *	Ethna_Error���֥������Ȥ���������(���顼��٥�:E_USER_NOTICE)
	 *
	 *	@access	public
	 *	@param	int		$code				���顼������
	 *	@param	string	$message			���顼��å�����(+����)
	 *	@static
	 */
	function &raiseNotice($code, $message = null)
	{
		$message_arg_list = array_slice(func_get_args(), 2);
		return new Ethna_Error(E_USER_NOTICE, $code, $message, $message_arg_list);
	}
}
?>

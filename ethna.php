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
define('VAR_TYPE_INT', 0);

/**	���Ƿ�: ��ư�������� */
define('VAR_TYPE_FLOAT', 2);

/**	���Ƿ�: ʸ���� */
define('VAR_TYPE_STRING', 1);

/**	���Ƿ�: ���� */
define('VAR_TYPE_DATETIME', 3);

/**	���Ƿ�: ������ */
define('VAR_TYPE_BOOLEAN', 4);

/**	���Ƿ�: �ե����� */
define('VAR_TYPE_FILE', 5);


/**	���顼������: DB��³���� */
define('E_DB_CONNECT', 1);

/**	���顼������: DB�����ꥨ�顼 */
define('E_DB_QUERY', 2);

/**	���顼������: ���å���󥨥顼 */
define('E_SESSION_INVALID', 16);

/**	���顼������: ���֥�������ID��ʣ���顼 */
define('E_APP_DUPOBJ', 32);

/**	���顼������: �ե�������ʸ���泌�顼 */
define('E_FORM_WRONGTYPE', 48);

/**	���顼������: �ե�������ɬ�ܥ��顼 */
define('E_FORM_REQUIRED', 49);

/**	���顼������: �ե������ͺǾ��ͥ��顼 */
define('E_FORM_MIN', 50);

/**	���顼������: �ե������ͺ����ͥ��顼 */
define('E_FORM_MAX', 51);

/**	���顼������: �ե�����������ʸ�����顼 */
define('E_FORM_INVALIDCHAR', 52);

/**	���顼������: �ե������������ͥ��顼 */
define('E_FORM_INVALIDVALUE', 53);

if (defined('E_STRICT') == false) {
	/**	PHP 5�Ȥθߴ��ݻ���� */
	define('E_STRICT', 0);
}
?>

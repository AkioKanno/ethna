<?php
/**
 *  Ethna_Util_Test.php
 */

/**
 *  Ethna_Config���饹�Υƥ��ȥ�����
 *
 *  @access public
 */
class Ethna_Config_Test extends UnitTestCase
{
    /**
     *  �����ͤؤΥ�������(R)�Υƥ���
     *
     *  @access public
     *  @param  string  $key    �������̾
     *  @return string  ������
     */
    function testGet($key = null)
    {
        $this->ctl =& new Ethna_Controller();
        $eConfig = new Ethna_Config($this->ctl);
        $actual = 1;
        $key = "key";
        $eConfig->set($key,$actual);
        $result = $eConfig->get($key);

        $this->assertEqual($result, $actual);
    }
    
    function testSet()
    {
        $this->ctl =& new Ethna_Controller();
        $eConfig = new Ethna_Config($this->ctl);
         
         $key = "key";
         $value = "value";
         $eConfig->set($key,$value);
         
         $result = $eConfig->get($key);
         
         $this->assertEqual($result,$value);
    }
    
    function testUpdate()
    {
        $this->ctl =& new Ethna_Controller();
        $eConfig = new Ethna_Config($this->ctl);

        $result = $eConfig->update();
    }
    function test_getConfigFile()
    {
         $this->ctl =& new Ethna_Controller();
        $eConfig = new Ethna_Config($this->ctl);

        $result = $eConfig->_getConfigFile(); 

        $file = $this->ctl->getDirectory('etc') . '/' . strtolower($this->ctl->getAppId()) . '-ini.php';

        $this->assertEqual($result,$file);
    }
    
    function test_getConfig()
    {
        $this->ctl =& new Ethna_Controller();
        $eConfig = new Ethna_Config($this->ctl);
 
         $eConfig->_getConfig();
    }
    
    function test_setConfig()
    {
        $this->ctl =& new Ethna_Controller();
        $eConfig = new Ethna_Config($this->ctl);
 
         $eConfig->_setConfig();
    }
    
    function test_setConfigValue()
    {
        $this->ctl =& new Ethna_Controller();
        $eConfig = new Ethna_Config($this->ctl);
        
        $file = $eConfig->_getConfigFile();
        $fp = fopen($file, 'w');
        $key = "key";
        $value = "value";
        $level = "1";
        $eConfig->_setConfigValue($fp, $key, $value, $level);
    }   
}
?>

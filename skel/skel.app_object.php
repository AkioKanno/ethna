<?php
class {$app_manager}Manager extends Ethna_AppManager
{
}

class {$app_object} extends Ethna_AppObject
{
    /**
     *  @var    array   �ơ��֥����
     */
    var $table_def = 
        array(
              '{$table}' => 
              array(
                    'primary' => true
                    ),
              );
    
    /**
     *  @var    array   �ץ�ѥƥ����
     */
    var $prop_def = array(
        {$prop_def}
              );
    
    function getName($key)
    {
        return $this->get($key);
    }
}
?>

<?php

use Adianti\Registry\TSession;

class W5ISessao
{    
	public static function incluirObjetoEdicaoSessao($object, $key, $primaryKey = null, $class = null)
    {
        try
        {
            if($class == null)
            {
                $class = __CLASS__;
            }
            $objectCopy = clone $object;
            //$objectCopy->keyValue = $key;
            if($primaryKey != null)
                $objectCopy->__set($primaryKey, $key);
            TSession::setValue($class.'.objectEdit',$objectCopy);
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

   	public static function obterObjetoEdicaoSessao(Object $object = null, $primaryKey = null, $readOnlyFields = null, $class = null)
    {
        try
        {
            if($class == null)
            {
                $class = __CLASS__;
            }
            
            $objectEdit = TSession::getValue($class.'.objectEdit');
            if($primaryKey != null && $object != null)
            {
                if($objectEdit != NULL)
                {
                    $object->__set($primaryKey, $objectEdit->{$primaryKey});
                    if($readOnlyFields != null && is_array($readOnlyFields))
                    {
                        foreach ($readOnlyFields as $field)
                        {
                            $object->__set($field, $objectEdit->__get($field));
                        }
                    }
                }
                else
                {
                    $object->__set($primaryKey, NULL);
                }
            }
            else
            {
                return $objectEdit;
            }
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
        public static function removerObjetoEdicaoSessao($class = null)
    {
        try
        {
            if($class == null)
            {
                $class = __CLASS__;
            }
           TSession::delValue($class.'.objectEdit');
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }    
}
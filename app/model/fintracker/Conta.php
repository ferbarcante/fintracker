<?php

use Adianti\Database\TRecord;

class Conta extends TRecord
{
    const TABLENAME = 'conta';
    const PRIMARYKEY ='id_conta';
    const IDPOLICY = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('id_conta');
        parent::addAttribute('nm_conta');
        parent::addAttribute('vl_saldo');
        parent::addAttribute('tp_conta');
    }


}
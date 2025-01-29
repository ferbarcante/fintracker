<?php

use Adianti\Database\TRecord;

class TipoConta extends TRecord 
{
    const TABLENAME = 'tipo_conta';
    const PRIMARYKEY = 'id_tipoconta';
    const IDPOLICY = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('id_tipoconta');
        parent::addAttribute('nm_tipoconta');
    }

}
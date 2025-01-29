<?php

use Adianti\Database\TRecord;

class Conta extends TRecord
{
    const TABLENAME = 'conta_bancaria';
    const PRIMARYKEY ='id_conta';
    const IDPOLICY = 'max';

    private $tipoConta;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('id_conta');
        parent::addAttribute('nm_conta');
        parent::addAttribute('vl_saldo');
        parent::addAttribute('id_tipoconta');
    }

    function get_tipo_conta()
    {
        if(empty($this->tipoConta))
        $this->tipoConta = new TipoConta($this->id_tipoConta);
    
        // returns the associated object
        return $this->tipoConta;
    }
}
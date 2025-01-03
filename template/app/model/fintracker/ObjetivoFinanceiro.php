<?php

use Adianti\Database\TRecord;

class ObjetivoFinanceiro extends TRecord
{
    const TABLENAME = 'objetivoFinanceiro';
    const PRIMARYKEY ='id_objetivoFinanceiro';
    const IDPOLICY = 'max';

    private $conta;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('id_objetivoFinanceiro');
        parent::addAttribute('vl_objetivo');
        parent::addAttribute('vl_atual');
        parent::addAttribute('id_conta');
    }

    function get_conta()
    {
        if(empty($this->conta))
        $this->conta = new Conta($this->id_conta);
    
        // returns the associated object
        return $this->conta;
    }
}
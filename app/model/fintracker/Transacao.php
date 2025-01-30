<?php

use Adianti\Database\TRecord;

class Transacao extends TRecord
{
    const TABLENAME = 'transacao';
    const PRIMARYKEY ='id_transacao';
    const IDPOLICY = 'max';

    private $categoria;
    private $conta;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('id_transacao');
        parent::addAttribute('vl_valor');
        parent::addAttribute('ds_descricao');
        parent::addAttribute('dt_transacao');
        parent::addAttribute('tp_transacao');
        parent::addAttribute('categoria_id');
        parent::addAttribute('conta_id');
    }

    function get_categoria()
    {
        if(empty($this->categoria))
        $this->categoria = new Categoria($this->id_categoria);
    
        // returns the associated object
        return $this->categoria;
    }

    function get_conta()
    {
        if(empty($this->conta))
        $this->conta = new Conta($this->id_conta);
    
        // returns the associated object
        return $this->conta;
    }
}
<?php

use Adianti\Database\TRecord;

class Categoria extends TRecord
{
    const TABLENAME = 'categoria';
    const PRIMARYKEY ='id_categoria';
    const IDPOLICY = 'max';

    private $tipoConta;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('id_categoria');
        parent::addAttribute('nm_categoria');
        parent::addAttribute('nm_cor');
    }
}
<?php

use Adianti\Database\TRecord;

class CalendarEvent extends TRecord
{
    const TABLENAME  = 'evento_calendario';
    const PRIMARYKEY = 'id_eventocalendario';
    const IDPOLICY   = 'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nu_tempoinicio');
        parent::addAttribute('nu_tempofim');
        parent::addAttribute('nm_cor');
        parent::addAttribute('nm_titulo');
        parent::addAttribute('ds_descricao');
    }
}
<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class ContaView extends TPage
{
    private $form, $cards, $datagrid, $pageNavigation;
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('fintracker');
        $this->setActiveRecord('Conta');
        $this->addFilterField('Nome');

        // criando form 
        $this->form = new BootstrapFormBuilder('form_search_Conta');
        $this->form->setFormTitle('Carteiras');

        $nome = new TEntry('nome');
        $this->form->addFields([new TLabel('Nome da Carteira:')]. [$nome]);

        $this->form->addAction('Procurar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Novo',  new TAction(['ContaForm', 'onEdit']), 'fa:plus green');

    }
}

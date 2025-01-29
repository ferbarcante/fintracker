<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TTransaction;
use Adianti\Validator\TRequiredListValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Wrapper\TDBMultiCombo;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class ContaForm extends TPage 
{
    protected $form;

    use Adianti\Base\AdiantiStandardFormListTrait;

    public function __construct()
   {
        parent::__construct();      
 
        $this->setAfterSaveAction( new TAction([$this, 'closeWindow' ]) );

        $this->form = new BootstrapFormBuilder('form_categores');
        $this->form->setFormTitle('Registre suas contas bancárias:');

        $nome = new TEntry('nm_conta');
        $valorSaldo = new TEntry('vl_saldo');
        $tipoConta = new TDBMultiCombo('tipoConta', 'fintracker', 'TipoConta', 'id_tipoconta', 'nm_tipoconta');
        
        $row = $this->form->addFields( [new TLabel('Nome da conta (*) ', '#FF0000', '14px', null, '100%'), $nome],
        [new TLabel('Saldo atual (*)', '#FF0000', '14px', null, '100%'), $valorSaldo], [new TLabel('Tipo da conta (*)', '#FF0000', '14px', null, '100%'), $tipoConta]);

        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $nome->addValidation('Nome', new TRequiredValidator);
        $valorSaldo->addValidation('Saldo', new TRequiredValidator);
        $tipoConta->addValidation('Tipo da Conta', new TRequiredValidator);

        $valorSaldo->setNumericMask(2, ',', '.', TRUE);

        $this->form->addAction( 'Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addActionLink('Limpar Formulário', new TAction([$this, 'onEdit']), 'fa:eraser red');

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);

        parent::add($vbox);

    }

    public function onSave()
    {
        try
        {
            TTransaction::open('fintracker');
            $this->form->validate();

            $formData = $this->form->getData();

            $conta = new Conta;
            $conta->fromArray((array) $formData);
            $conta->store();

            $formData->id_conta = $conta->id_conta;
            $this->form->setData($formData);

            TTransaction::close();
            new TMessage('info', 'Registro salvo com sucesso!');
        }
        catch (Exception $e)
        {
            $this->form->setData($this->form->getData());
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        } finally
        {
            TTransaction::close();
        }
    }
}
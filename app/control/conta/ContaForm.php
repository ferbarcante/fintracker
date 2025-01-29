<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TTransaction;
use Adianti\Validator\TRequiredListValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Base\TScript;
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
    public $form;

    // use Adianti\Base\AdiantiStandardFormListTrait;

    public function __construct()
   {
        parent::__construct();      
 
        // $this->setAfterSaveAction( new TAction([$this, 'closeWindow' ]) );
        
        if (!$this->embedded)
        {
            parent::setTargetContainer('adianti_right_panel');
        }

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
        $this->form->addHeaderActionLink('Fechar', new TAction([$this, 'onClose']), 'fa:times red');
        $this->form->addAction('Editar', new TAction([$this, 'Edit']), 'far:edit blue');

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);

        parent::add($vbox);

    }

    public function onSave()
    {
        $contaService = new ContaService;
        $contaService->Criar();
    }

    function onClose()
    {
        TScript::create("Template.closeRightPanel()");
        W5ISessao::removerObjetoEdicaoSessao(__CLASS__);
    }

    public function onEdit($param = null)
    {
        try
        {
            TTransaction::open('fintracker');

            if(isset($param['id_conta']))
            {
                $idConta = $param['id_conta'];
                $conta = new Conta($idConta);

                W5ISessao::incluirObjetoEdicaoSessao($conta, $idConta, 'id_conta', __CLASS__);

                $this->form->setEditable(false);
                $this->form->setdata($conta);
            }
        } 
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        finally
        {
            TTransaction::close();
        }
    }

    function Edit($param = NULL)
    {
        try 
        {
            $data = $this->form->getData();

            $this->form->setData($data);
            TTransaction::open('fintracker');
            
            $conta = new Conta();
            
            W5ISessao::obterObjetoEdicaoSessao($conta, 'id_conta', null, __CLASS__);
            
        } 
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        } 
        finally 
        {
            TTransaction::close();
        }
    }
}
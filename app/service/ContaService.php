<?php

use Adianti\Control\TAction;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;

class ContaService
{
    private $contaForm;

    public function __construct()
    {
        $this->contaForm = new ContaForm();
    }

    public function criar()
    {
        try
        {   
            TTransaction::open('fintracker');
            $this->contaForm->form->validate();
            $formData = $this->contaForm->form->getData();
            $conta = new Conta;
            $conta->fromArray((array) $formData);

            W5ISessao::obterObjetoEdicaoSessao($conta, 'id_conta', null, __CLASS__);

            $conta->nm_conta = $formData->nm_conta;
            $conta->vl_saldo = $formData->vl_saldo;
            $conta->id_tipoconta = $formData->id_tipoconta;
            
            $conta->store();

            $posAction = new TAction(['ContaView', 'onReload']);
            TTransaction::close();
            new TMessage('info', 'Registro salvo com sucesso!', $posAction);
        }
        catch (Exception $e)
        {
            $this->contaForm->form->setData($this->contaForm->form->getData());
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        } finally
        {
            TTransaction::close();
        }
    }

    public function editar($data)
    {
        try 
        {
            $this->contaForm->form->setData($data);
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
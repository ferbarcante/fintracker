<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TCardView;
use Adianti\Widget\Util\TXMLBreadCrumb;
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
        $this->form->addFields([new TLabel('Nome da Carteira:')], [$nome]);

        $this->form->addAction('Procurar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Novo',  new TAction(['ContaForm', 'onEdit']), 'fa:plus green');

        // keep the form filled with the search data
        $nome->setValue( TSession::getValue( 'Conta_nome' ) );
      
        // creates the Card View
        $this->cards = new TCardView;
        $this->cards->setContentHeight(170);
        $this->cards->setTitleAttribute('{nm_conta} (#{id_conta})'); // oq precisa preencher aqui?

        $this->setCollectionObject($this->cards);
        
        $this->cards->setItemTemplate('<div style="float:left;width:50%;padding-right:10px">
                                           <b>Nome</b> <br> {nm_conta} <br>
                                           <b>Saldo</b> <br> {vl_saldo} <br>
                                           <b>Tipo</b> <br> {id_tipoconta}
                                       </div>
                                       <div style="float:right;width:50%">
                                           <img style="height:100px;float:right;margin:5px" src="{photo_path}">
                                       </div> '
        );
        
        $edit_action   = new TAction(['ContaForm', 'onEdit'], ['id_conta'=> '{id_conta}']);
        $delete_action = new TAction([$this, 'onDelete'], ['id_conta'=> '{id_conta}', 'register_state' => 'false']);
        
        $this->cards->addAction($edit_action,   'Edit',   'far:edit blue');
        $this->cards->addAction($delete_action, 'Delete', 'far:trash-alt red');
          
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        // creates the page structure using a table
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        // $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form); // add a row to the form
        $vbox->add(TPanelGroup::pack('', $this->cards, $this->pageNavigation)); // add a row for page navigation
        
        // add the table inside the page
        parent::add($vbox);
    }

    public function onEdit($param = NULL)
    {
        try 
        {
            if(isset($param['id_conta']))
            {
                $idConta = $param['id_conta'];
                TTransaction::open('fintracker');
                $tipoConta = new TipoConta($idConta);
                $this->form->setData($tipoConta);
                $this->onReload();
            } 
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        } finally 
        {
            TTransaction::close();
        }
    }

    public static function onDelete($param = NULL)
    {
        try 
        {  
            $action = new TAction([__CLASS__, 'Delete']);
            $action->setParameters($param);
    
            new TQuestion('Deseja realmente excluir a conta bancária?', $action);
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }

       
    public static function Delete($param = NULL)
    {
        try 
        {  
            TTransaction::open('fintracker'); // Abre a transação
    
            $idConta = $param['id_conta'];
    
            $conta = new Conta($idConta, FALSE);
            $conta->delete();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', 'Registro excluído com sucesso!', $pos_action);
    
            TTransaction::close(); // Finaliza a transação
        } 
        catch (Exception $e)
        {
            // Exibe a mensagem de erro
            new TMessage('error', $e->getMessage());
            TTransaction::rollback(); // Reverte a transação
        }
    }
    
}


<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TCardView;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class TransacaoView extends TPage 
{
    private $form;
    private $datagrid;
    private $pageNavigation;

    use Adianti\Base\AdiantiStandardListTrait;
    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('fintracker');
        $this->setActiveRecord('Transacao');
        $this->addFilterField('dt_transacao', 'like');
        $this->addFilterField('tp_transacao', 'like');

        // criando o form de filtro
        $this->form = new BootstrapFormBuilder('form_search_Transacao');
        $this->form->setFormTitle('Transações financeiras');

        $dataTransacao = new TDate('dataTransacao');
        $tipoTransacao = new TEntry('tipoTransacao');

        $this->form->addFields( [ new TLabel('Data da Transação:') ], [ $dataTransacao ]);
        $this->form->addFields( [ new TLabel('Tipo de Transação:') ], [ $tipoTransacao ]);

        // form actions 
        $this->form->addAction('Procurar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Nova Transação', new TAction(['TransacaoForm','onClear']), 'fa:plus-cricle green');
        
        // manter as infos da barra de pesquisa ao atualizar
        $this->form->setData( TSession::getValue('StandardDataGridView_filter_data'));
        
        // criando datagrid 
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = "100%";

        $colDescricao = new TDataGridColumn('ds_descricao', 'Descricão', 'left', '30%');
        $colContaBancaria = new TDataGridColumn('nm_conta', 'Conta', 'left', '30%');
        $colCategoria = new TDataGridColumn('nm_categoria', 'Categoria', '20%');
        $colTipoTransacao = new TDataGridColumn('tp_transacao', 'Tipo', 'left', '20%');
        $colData = new TDataGridColumn('dt_transacao', 'Data', 'left', '20%');
        $colValor = new TDataGridColumn('vl_valor', 'Valor', 'left', '10%');

        $this->datagrid->addColumn($colDescricao);
        $this->datagrid->addColumn($colContaBancaria);
        $this->datagrid->addColumn($colCategoria);
        $this->datagrid->addColumn($colTipoTransacao);
        $this->datagrid->addColumn($colData);
        $this->datagrid->addColumn($colValor);

        $acao1 = new TDataGridAction(['TransacaoForm', 'onEdit'], ['key' => '{id_transacao}']);
        $acao2 = new TDataGridAction([$this, 'onDelete'], ['key' => '{id_transacao}']);

        $this->datagrid->addAction($acao1, 'Edit', 'far:edit blue');
        $this->datagrid->addAction($acao2, 'Delete', 'far:trash-alt red');

        $this->datagrid->createModel();

        // criando page navigation
         
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        
        // cartões
        $cards = new TCardView;
        $items = [];
        
        $items[] = (object) [ 'id' => 1, 'title' => 'Saldo atual', 'content' => , 'color' => '#415a77'];
        $items[] = (object) [ 'id' => 2, 'title' => 'Receitas', 'content' => 'item 2 content', 'color' => '#415a77'];
        $items[] = (object) [ 'id' => 3, 'title' => 'Despesas', 'content' => 'item 3 content', 'color' => '#415a77'];

        foreach ($items as $key => $item)
        {
            $cards->addItem($item);
        }

        $cards->setTitleAttribute('title');
        $cards->setColorAttribute('color');

        $cards->setItemTemplate('<b>Content</b>: {content}');
        $edit_action   = new TAction([$this, 'onItemEdit'], ['id'=> '{id}']);
        $delete_action = new TAction([$this, 'onItemDelete'], ['id'=> '{id}']);
        $cards->addAction($edit_action,   'Edit',   'far:edit blue');
        $cards->addAction($delete_action, 'Delete', 'far:trash-alt red');
        
        $vbox1 = new TVBox;
        $vbox1->style = 'width: 100%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox1->add($cards);    
        $vbox1->add($this->form);  
        $vbox1->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        parent::add($vbox1);
    }

     /**
     * Item edit action
     */
    public static function onItemEdit($param = NULL)
    {
        new TMessage('info', '<b>onItemEdit</b><br>' . str_replace('","', '",<br>&nbsp;"', json_encode($param)));
    }
    
    /**
     * Item delete action
     */
    public static function onItemDelete($param = NULL)
    {
        new TMessage('info', '<b>onItemDelete</b><br>' . str_replace('","', '",<br>&nbsp;"', json_encode($param)));
    }

    /**
     * Clear filters
     */
    function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}
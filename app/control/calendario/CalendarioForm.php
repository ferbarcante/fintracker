<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TColor;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TText;
use Adianti\Wrapper\BootstrapFormBuilder;

class CalendarioForm extends TWindow
{
    protected $form;

    public function __construct()
    {
        parent::__construct();
        parent::setSize(700, null);
        parent::setTitle('Planejamento financeiro');
        parent::removePadding();

        $this->form = new BootstrapFormBuilder('form_event');
        $this->form->setProperty('class', 'card panel noborder');
        $this->form->setProperty('style', 'margin-bottom:0');
        
        $hours = array();
        $minutes = array();
        for ($n=0; $n<24; $n++)
        {
            $hours[$n] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }
        
        for ($n=0; $n<=55; $n+=5)
        {
            $minutes[$n] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }

         // create the form fields
         $view           = new THidden('view');
         $color          = new TColor('cor');
         $start_date     = new TDate('start_date');
         $start_hour     = new TCombo('start_hour');
         $start_minute   = new TCombo('start_minute');
         $end_date       = new TDate('end_date');
         $end_hour       = new TCombo('end_hour');
         $end_minute     = new TCombo('end_minute');
         $title          = new TEntry('title');
         $description    = new TText('description');
         $color->setValue('#3a87ad');

         $start_hour->addItems($hours);
         $start_minute->addItems($minutes);
         $end_hour->addItems($hours);
         $end_minute->addItems($minutes);

         $start_hour->setChangeAction(new TAction(array($this, 'onChangeStartHour')));
         $end_hour->setChangeAction(new TAction(array($this, 'onChangeEndHour')));
         $start_date->setExitAction(new TAction(array($this, 'onChangeStartDate')));
         $end_date->setExitAction(new TAction(array($this, 'onChangeEndDate')));
         
         $this->form->addFields( [$view] );
         $this->form->addFields( [new TLabel('Color:')], [$color] );
         $this->form->addFields( [new TLabel('Start time:')], [$start_date, $start_hour, ':', $start_minute] );
         $this->form->addFields( [new TLabel('End time:')], [$end_date, $end_hour, ':', $end_minute] );
         $this->form->addFields( [new TLabel('Title:')], [$title] );
         $this->form->addFields( [new TLabel('Description:')], [$description] );
         
         $this->form->addAction( _t('Save'),   new TAction(array($this, 'onSave')),   'fa:save green');
         $this->form->addAction( _t('Clear'),  new TAction(array($this, 'onEdit')),   'fa:eraser orange');
         $this->form->addAction( _t('Delete'), new TAction(array($this, 'onDelete')), 'far:trash-alt red');
         
         parent::add($this->form);
    }

        /**
     * Executed when user leaves start hour field
     */
    public static function onChangeStartHour($param=NULL)
    {
        $obj = new stdClass;
        if (empty($param['start_minute']))
        {
            $obj->start_minute = '0';
            TForm::sendData('form_event', $obj);
        }
        
        if (empty($param['end_hour']) AND empty($param['end_minute']))
        {
            $obj->end_hour = $param['start_hour'] +1;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves end hour field
     */
    public static function onChangeEndHour($param=NULL)
    {
        if (empty($param['end_minute']))
        {
            $obj = new stdClass;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
        /**
     * Executed when user leaves start date field
     */
    public static function onChangeStartDate($param=NULL)
    {
        if (empty($param['end_date']) AND !empty($param['start_date']))
        {
            $obj = new stdClass;
            $obj->end_date = $param['start_date'];
            TForm::sendData('form_event', $obj);
        }
    }
        /**
     * Executed when user leaves end date field
     */
    public static function onChangeEndDate($param=NULL)
    {
        if (empty($param['end_hour']) AND empty($param['end_minute']) AND !empty($param['start_hour']))
        {
            $obj = new stdClass;
            $obj->end_hour = min($param['start_hour'],22) +1;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }

     /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    public function onSave()
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('fintracker');
            
            $this->form->validate(); // form validation
            
            // get the form data into an active record Entry
            $data = $this->form->getData();
            
            $object = new CalendarEvent;
            $object->nm_cor = $data->color;
            // $object->id = $data->id;
            $object->nm_titulo = $data->title;
            $object->ds_descricao = $data->description;
            $object->nu_tempoinicio = $data->start_date . ' ' . str_pad($data->start_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->start_minute, 2, '0', STR_PAD_LEFT) . ':00';
            $object->nu_tempofim = $data->end_date . ' ' . str_pad($data->end_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->end_minute, 2, '0', STR_PAD_LEFT) . ':00';
            
            $object->store(); // stores the object
            
            $data->id = $object->id;
            $this->form->setData($data); // keep form data
            
            TTransaction::close(); // close the transaction
            $posAction = new TAction(array('FullCalendarDatabaseView', 'onReload'));
            $posAction->setParameter('view', $data->view);
            $posAction->setParameter('date', $data->start_date);
            
            // shows the success message
            new TMessage('info', 'Registro salvo com sucesso!', $posAction);
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            $this->form->setData( $this->form->getData() ); // keep form data
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {

    }
    
    public function onDelete($param)
    {

    }

    public function Delete()
    {

    }

    public function onStartEdit()
    {

    }

    public function onUpdateEvent()
    {

    }

    public function onUpdate()
    {

    }



}
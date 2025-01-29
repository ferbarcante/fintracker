<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Util\TFullCalendar;

class CalendarioView extends TPage
{

    private $fc;

    public function __construct()   
    {
        parent::__construct();

        $this->fc = new TFullCalendar(date('Y-m-d'), 'month');
        $this->fc->setReloadAction(new TAction(array($this, 'getEvents')));
        $this->fc->setDayClickAction(new TAction(array('CalendarioForm', 'onStartEdit')));
        $this->fc->setEventClickAction(new TAction(array('CalendarioForm', 'onEdit')));
        $this->fc->setEventUpdateAction(new TAction(array('CalendarioForm', 'onUpdate')));
        $this->fc->enableFullHeight();

        $this->fc->setOption('businessHours', [ [ 'dow' => [ 1, 2, 3, 4, 5 ], 'start' =>'08:00', 'end' => '18:00']]);
        parent::add(TPanelGroup::pack('', $this->fc));
    }

    public static function getEvents($param = NULL)
    {
        $return = array();

        try
        {
            TTransaction::open('fintracker');
            $events = CalendarEvent::where('nu_tempoinicio', '<=', $param['end'])->where('nu_tempofim', '>=', $param['start'])->load();

            if($events)
            {
                foreach ($events as $event)
                {
                    $event_array = $event->toArray();
                    $event_array['start'] = str_replace( ' ', 'T', $event_array['nu_tempoinicio']);
                    $event_array['end'] = str_replace( ' ', 'T', $event_array['nu_tempofim']);
                   
                    if (!isset($event_array['nm_titulo'])) {
                        var_dump($event_array);
                        throw new Exception("Erro: chave 'nm_titulo' não encontrada no evento ID: {$event_array['id_eventocalendario']}");
                    }

                    $popover_content = $event->render("<b>Título</b>: {nm_titulo} <br> <b>Descrição</b>: {ds_descricao}");
                    $event_array['title'] = TFullCalendar::renderPopover($event_array['nm_titulo'], 'Popover title', $popover_content);

                    $return[] = $event_array;
                }
            } 

            TTransaction::close();
            echo json_encode($return);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    } 

        /**
     * Reconfigure the callendar
     */
    public function onReload($param = null)
    {
        if (isset($param['view']))
        {
            $this->fc->setCurrentView($param['view']);
        }
        
        if (isset($param['date']))
        {
            $this->fc->setCurrentDate($param['date']);
        }
    }
}
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

        $this->fc = new TFullCalendar(date('d-m-Y'), 'month');
        $this->fc->setReloadAction(new TAction(array($this, 'getEvents')));
        $this->fc->setDayClickAction(new TAction(array('CalendarEventForm', 'onStartEdit')));
        $this->fc->setEventClickAction(new TAction(array('CalendarEventForm', 'onEdit')));
        $this->fc->setEventUpdateAction(new TAction(array('CalendarEventForm', 'onUpdate')));
        $this->fc->enableFullHeight();

        $this->fc->setOption('businessHours', [ [ 'dow' => [ 1, 2, 3, 4, 5 ], 'start' =>'oo:oo', 'end' => '18:00']]);
        parent::add(TPanelGroup::pack('', $this->fc));
    }

    public static function getEvents($param = NULL)
    {
        $return = array();

        try
        {
            TTransaction::open('fintracker');
            $events = CalendarEvent::where('start_time', '<=', $param('end'))->where('end_time', '>=', $param['start'])->load();

            if($events)
            {
                foreach ($events as $event)
                {
                    $event_array = $event->toArray();
                    $event_array['start'] = str_replace( ' ', 'T', $event_array['start_time']);
                    $event_array['end'] = str_replace( ' ', 'T', $event_array['end_time']);

                    $popover_content = $event->render("<b>Título</b>: {title} <br> <b>Descrição</b>: {description}");
                    $event_array['title'] = TFullCalendar::renderPopover($event_array['title'], 'Popover title', $popover_content);

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
}
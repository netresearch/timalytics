<?php
require_once __DIR__ . '/../src/bootstrap.php';

$user = loadUsername();
loadUserSelection($db);

$strTitle = 'Kalender';
$additionalHeader = <<<HTM
  <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.9.0/fullcalendar.min.css' rel='stylesheet' />
  <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.9.0/fullcalendar.print.css' rel='stylesheet' media='print' />
  <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.9.0/fullcalendar.min.js'></script>
  <link href='https://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.css' rel='stylesheet' />
  <script src='https://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.js'></script>
  <style>
	body {
		margin: 0;
		padding: 0;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		font-size: 14px;
	}

	#script-warning {
		display: none;
		background: #eee;
		border-bottom: 1px solid #ddd;
		padding: 0 10px;
		line-height: 40px;
		text-align: center;
		font-weight: bold;
		font-size: 12px;
		color: red;
	}

	#loading {
		display: none;
		position: absolute;
		top: 10px;
		right: 10px;
	}

	#calendar {
		max-width: 900px;
		margin: 40px auto;
		padding: 0 10px;
	}

  </style>
HTM;
include __DIR__ . '/../data/templates/head.tpl.php';
?>
  <script>
    $(document).ready(function() {	
	$('#calendar').fullCalendar({
	    header: {
		left: 'prev,next today',
		center: 'title',
		right: 'month,agendaWeek,agendaDay'
	    },
            //defaultDate: '2014-11-12',
	    editable: false,
            defaultView: 'agendaWeek',
            firstDay: 1,
            allDaySlot: false,
            timeFormat: 'H:mm',
            axisFormat: 'H:mm',
            //slotDuration: '00:15:00',
	    eventLimit: true, // allow "more" link when too many events
	    events: {
		url: 'ical.php?format=json&user=<?php echo $user; ?>',
		error: function() {
		    $('#script-warning').show();
		}
	    },
	    loading: function(bool) {
		$('#loading').toggle(bool);
	    },
            eventRender: function(event, element) {
                element.qtip({
                    content: event.description
                });
	    }
	});
    });
  </script>
  <div id='script-warning'>
    Error fetching events
  </div>
    
  <div id='loading'></div>

  <div id='calendar'></div>
  <p style="text-align: center">
   <a href="ical.php?user=<?php echo $user; ?>">iCal download</a>
  </p>
<?php
include __DIR__ . '/../data/templates/foot.tpl.php';
?>

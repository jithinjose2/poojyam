$(document).ready(function() {
		
    serverUrl = 'ws://poojyam.in:8000/demo';
    if (window.MozWebSocket) {
        socket = new MozWebSocket(serverUrl);
    } else if (window.WebSocket) {
        socket = new WebSocket(serverUrl);
    }
    socket.binaryType = 'blob';
    socket.onopen = function(msg) {
        $('#status').html('<span class="label label-info">'+l72+'</span>');
        authenticate();
        return true;
    };
    
    socket.onmessage = function(msg) {
        var response;
        response = JSON.parse(msg.data);
		
		
		if(response.action=='game_invite'){
			$('#chatAudio')[0].play();
			var message = '<img src="'+response.image+'" width=20px/>&nbsp;&nbsp;'+response.name+' '+l73+'&nbsp;&nbsp;';
			message = message + '<a class="btn btn-info" href="http://poojyam.in/game_join/'+response.game_id+'">'+l61+'</a>';
			message = message + '&nbsp;&nbsp;<button class="btn btn-warning game_reject" game_id="'+response.game_id+'" user_id="'+response.user_id+'">'+l63+'</button>';
			
			message= '<div class="alert alert-info">'+message+'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
			
			$(".message_box").empty();
			$(".message_box").append(message);
		}else{
			checkJson(response);
		}
        return true;
    };
    socket.onclose = function(msg) {
        $('#status').html('<span class="label label-danger">'+l74+'</span>');
        setTimeout(function(){
			$('#status').html('<span class="label label-warning">'+l75+'</span>');
			location.reload();
		}
        ,2500);
        return true;
    };
	
	$( "body" ).delegate( ".game_reject", "click", function() {
		var game_id = $(this).attr('game_id');
		var user_id = $(this).attr('user_id');
		var last_game_rejector = $(this);
		$.ajax({url:'http://poojyam.in/ajax_interface.php?action=game_reject&game_id='+game_id+"&user_id="+user_id}).done(function(data){
			console.log('Done function fired');
			last_game_rejector.parent().slideUp();
		});
	});
	
	
	$("#switch_lan").click(function(){
		
		var clan = $("#switch_lan").attr('lan');
		lan = 'eng';
		if (clan=='eng') {
			lan  = 'mal';
		}
		
		window.location = window.location.href + '?lan=' + lan;
	})
	
});

function authenticate() {
    payload = new Object();
    payload.action 		= 'authenticate';
    payload.secure_hash = secure_hash;
    payload.user_id 	= user_id;
	if (typeof game_id != 'undefined'){
		payload.game_id 	= game_id;
	}
    socket.send(JSON.stringify(payload));
}

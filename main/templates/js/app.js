var globalVar = {
	'pages' : {
		'tasks' : '#tasks',
		'archive' : '#archive',
	},
};
var ajaxController = 'ajaxController.php';





function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}




function renderTasks(){
	$('.task-wraper').remove();
	for (var i=0;i<globalVar.tasks.length; i++){
		var task = globalVar.tasks[i];
		var taskHtml='';
		var priority = !+task.priority ? '' : ' high';
		var tmp = !+task.status ? '' : 'done';
		taskHtml = taskHtml + '<div class="task-wraper '+ tmp + priority +'" id="task-'+task.id+' ">'
		taskHtml = taskHtml + '<div onclick="moreinfo(this)" class="moreinfo"><span class="glyphicon glyphicon-chevron-down"></span></div>';
		taskHtml = taskHtml + '<div onclick="removeTask(this)" class="delete"><span class="glyphicon glyphicon-remove"></span></div>';
		taskHtml = taskHtml + '<div onclick="updateTaskDone(this)" class="task">';
		taskHtml = taskHtml + '<p class="text">'+task.text+'</p>';
		tmp = +task.finish_to ? 'finish-to: '+ timeConverter(task.finish_to) : '' ;
		taskHtml = taskHtml + '<p class="finish-to"><span class="finish-to">'+tmp+'</span></p>';
		tmp = !+task.priority ? 'normal' : 'high';
		taskHtml = taskHtml + '<p class="priority">priority <span class="prority">'+tmp+'</span></p>';
		taskHtml = taskHtml + '</div>';
		taskHtml = taskHtml + '</div>';

		if(+task.status){
			var nest = $('#archive .tasks');
		}else{
			var nest = $('#tasks .tasks');
		}
		nest.append($(taskHtml));
	}
}

function getTasks(){
	var data = {};
	data.token = globalVar.token;
	data.controller = 'getTasks';
	$.post(ajaxController,data,function(response, status){
		globalVar.tasks = JSON.parse(response);
		
		return renderTasks();

	});
}


function addTask(data){
	data.token = globalVar.token;
	data.controller = 'createTask';

	data['finish_to'] = Math.round(new Date(data['finish_to']).getTime()/1000);
	
	data = toObject(data);
	if(!data.text) return;
	$.post(ajaxController,data,function(response, status){
		return getTasks();
	});
}

function removeTask(elm){
	id = $(elm).parent('.task-wraper').attr('id');
	id = id.split('-').pop().trim();
	var data = {};
	data.token = globalVar.token;
	data.controller = 'removeTask';
	data.taskId = id;
	data = toObject(data);
	$.post(ajaxController,data,function(response, status){
		return getTasks();
	});
}

function updateTaskDone(elm){
	id = $(elm).parent('.task-wraper').attr('id');
	id = id.split('-').pop().trim();
	
	var data = {};
	data.token = globalVar.token;
	data.controller = 'updateTaskDone';
	data.taskId = id;
	for(var i in globalVar.tasks){
		if(globalVar.tasks[i].id==id){
			data.status = globalVar.tasks[i].status;
			break;
		}
	}
	data = toObject(data);
	$.post(ajaxController,data,function(response, status){
		return getTasks();
	});
}







$( "form" ).on( "submit", function( event ) {
  event.preventDefault();
  var id = this.id;
  var data = $( this ).serializeArray();
  for (prop in data){
  	data[data[prop].name] = data[prop].value;
  	delete data[prop];
  }
  window[id](data);
  this.reset();
});

function register(data){
	data = toObject(data);
	data.controller = 'register';
	if(data.password !== data.password1){
		$('#register .message').text('passwords do not mutch');
		return false;
	}
	$.post(ajaxController,data,function(response, status){
		if(response=='email or login already has been taken'){
			$('#register .message').text(response);
			return false;
		}

		response = JSON.parse(response);
		logInSuccess(response.token);
		var date = new Date(new Date().getTime() + 60*60 * 1000);
		document.cookie = "token="+response.token+"; path=/; expires=" + date.toUTCString();
		globalVar.user = response.user;
	});
}

function logIn(data){
	data = toObject(data);
	data.controller = 'logIn';
	$.post(ajaxController,data,function(response, status){
		if (response=='wrong mail or password'){
			$('#login .message').text(response);
			return false;
		}
		response=JSON.parse(response);
		var date = new Date(new Date().getTime() + 60*60 * 1000);
		document.cookie = "token="+response.token+"; path=/; expires=" + date.toUTCString();
		globalVar.user = response.user;
		logInSuccess(response.token);
	});
}

function logInSuccess(token){
	if(token){
		globalVar.token = token;
	}
	var heigth = $(window).height();
	var elm = $('ul li')[0];
	displayTasks(elm);
	$('.enter-app').css('top',-heigth-100);
	$('#hello-user').text('Hi '+globalVar.user.nickname);
	getTasks();
}

function islogedIn(){
	
	var data = {
		'token': getCookie('token'),
		'controller': 'getUserByToken',
	};
	if(data.token){
		$.post(ajaxController,data,function(response, status){
			response = JSON.parse(response);
			if(response.status){
				globalVar.user = response.user;
				logInSuccess(data.token);
			}
		});
	}
}

function displayTasks(elm){
	resetPages();
	resetNavMenu();
	$(elm).addClass('active')
	$(globalVar.pages.tasks).addClass('active');
};
function displayArchive(elm){
	resetPages();
	resetNavMenu();
	$(elm).addClass('active')
	$(globalVar.pages.archive).addClass('active');	
};

//function logOut();

function resetPages(){

	for (var page in globalVar.pages){
		$(globalVar.pages[page]).removeClass('active');
	}
}
function resetNavMenu(){
	$('.nav.navbar-nav li').removeClass('active');
}

$('#display-options').click(function(){
	if($('#more-options').hasClass('active')){
		$('#more-options').removeClass('active');
	}else{
		$('#more-options').addClass('active');
	}
});


function moreinfo(elm){
	var elm = $(elm).closest('.task-wraper');
	if(elm.hasClass('moreinfo')){
		elm.removeClass('moreinfo');
	}else{
		elm.addClass('moreinfo');
	}
}



function toObject(arr) {
  var rv = {};
  for (var i in arr)
    rv[i] = arr[i];
  return rv;
}

function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}
function logOut(){
	var date = new Date(new Date().getTime() - 60*60 * 1000);
	document.cookie = "token=; path=/; expires=" + date.toUTCString();
	$('.enter-app').css('top','');
}
islogedIn();


function isEmpty(obj) {

    // null and undefined are "empty"
    if (obj == null) return true;

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    // If it isn't an object at this point
    // it is empty, but it can't be anything *but* empty
    // Is it empty?  Depends on your application.
    if (typeof obj !== "object") return true;

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}


function timeConverter(UNIX_timestamp){
  var a = new Date(UNIX_timestamp * 1000);
  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var year = a.getFullYear();
  var month = months[a.getMonth()];
  var date = a.getDate();
  var time = date + '.' + month + '.' + year ;
  return time;
}
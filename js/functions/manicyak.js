function initiate(id,btn) {
	$.ajax({
		type: "POST",
		url: "/api/initiate/" + id
	});
	
	$(btn).removeAttr('onclick');
	$(btn).attr('disabled','disabled');
	$(btn).html('Gathering...');
}

function watch(id,ack) {
	if (ack) {
		doack(id);
	}
	var top = (screen.height/2)-240;
	var left = (screen.width/2)-320;
	window.open("/watch/?id=" + id,"videowindow","resizable=no,toolbar=no,scrollbars=no,menubar=no,status=no,directories=no,width=640,height=480,left=" + left + ",top=" + top);
}

function trailer(url) {
	var top = (screen.height/2)-240;
	var left = (screen.width/2)-320;
	window.open("//www.youtube.com/embed/" + url + '?autoplay=1',"videowindow","resizable=no,toolbar=no,scrollbars=no,menubar=no,status=no,directories=no,width=640,height=480,left=" + left + ",top=" + top);
}

function checktv(series,season,episode,btn) {
	var response;

	$.ajax({
		type: "POST",
		url: "/api/tv/" + series + "/" + season + "/" + episode
	}).done(function(msg) {
		response = $.parseJSON(msg);
		if (response['status']=="unavailable") {
			$(btn).removeAttr('onclick');
			$(btn).attr('class','btn btn-mini btn-danger');
			$(btn).html('Unavailable');		
		} else if (response['status']=="none") {
			$(btn).removeAttr('disabled');
			$(btn).attr('onclick','initiate(' + response['id'] + ',this)');
			$(btn).html('Gather');
			$(btn).attr('class','btn btn-mini btn-info');
		} else if (response['status']=="partial") {
			$(btn).html('Gathering...');
			$(btn).attr('class','btn btn-mini btn-info');
		} else if (response['status']=="complete") {
			$(btn).removeAttr('disabled');
			$(btn).attr('onclick','download(' + response['id'] + ')');
			$(btn).html('Download');
			$(btn).attr('class','btn btn-mini btn-info');
		} else if (response['status']==null) {
			checktv(series,season,episode,btn); // hack
		}
	});
	$(btn).attr('disabled','disabled');
	$(btn).html('Checking...');
}

function checkmusic(artist,album,btn) {
	var response;

	$.ajax({
		type: "POST",
		url: "/api/music/" + artist + "/" + album
	}).done(function(msg) {
		response = $.parseJSON(msg);
		if (response['status']=="unavailable") {
			$(btn).removeAttr('onclick');
			$(btn).attr('class','btn btn-danger');
			$(btn).html('Unavailable');		
		} else if (response['status']=="none") {
			$(btn).removeAttr('disabled');
			$(btn).attr('onclick','initiate(' + response['id'] + ',this)');
			$(btn).html('Gather');
			$(btn).attr('class','btn btn-info');
		} else if (response['status']=="partial") {
			$(btn).html('Gathering...');
			$(btn).attr('class','btn btn-info');
		} else if (response['status']=="complete") {
			$(btn).removeAttr('disabled');
			$(btn).attr('onclick','download(' + response['id'] + ')');
			$(btn).html('Download');
			$(btn).attr('class','btn btn-info');
		} else if (response['status']==null) {
			checkmusic(artist,album,btn); // hack
		}
	});
	$(btn).attr('disabled','disabled');
	$(btn).html('Checking...');
}

function checkmovie(name,hd,btn) {
	var response;

	$.ajax({
		type: "POST",
		url: "/api/movie/" + name + "/" + hd
	}).done(function(msg) {
		response = $.parseJSON(msg);
		if (response['status']=="unavailable") {
			$(btn).removeAttr('onclick');
			$(btn).attr('class','btn btn-danger');
			if (hd) {
				$(btn).html('HD Unavailable');	
			} else {
				$(btn).html('Standard Unavailable');	
			}
		} else if (response['status']=="none") {
			$(btn).removeAttr('disabled');
			$(btn).attr('onclick','initiate(' + response['id'] + ',this)');
			if (hd) {
				$(btn).html('Gather (HD)');
				$(btn).attr('class','btn btn-success');
			} else {
				$(btn).html('Gather (Standard)');
				$(btn).attr('class','btn btn-warning');
			}
		} else if (response['status']=="partial") {
			if (hd) {
				$(btn).html('Gathering... (HD)');
				$(btn).attr('class','btn btn-success');
			} else {
				$(btn).html('Gathering... (Standard)');
				$(btn).attr('class','btn btn-warning');
			}
		} else if (response['status']=="complete") {
			$(btn).removeAttr('disabled');
			$(btn).attr('onclick','download(' + response['id'] + ')');
			if (hd) {
				$(btn).html('Download (HD)');
				$(btn).attr('class','btn btn-success');
			} else {
				$(btn).html('Download (Standard)');
				$(btn).attr('class','btn btn-warning');
			}
		} else if (response['status']==null) {
			checkmovie(name,hd,btn); // hack
		}
	});
	$(btn).attr('disabled','disabled');
	$(btn).html('Checking...');
}

function download(id,ack) {
	if (ack)
		doack(id);
	window.open("/download/?id=" + id,"Download");
}

function doDelete(id) {
	bootbox.confirm("Are you sure you want to delete this item?", function(confirmed) {
		if (confirmed) {
			$.ajax({
				type: "POST",
				url: "/api/delete/" + id
			});
			$('#media-row-' + id).remove();
		}
	});
}

function updateSidebar() {
	var response;
	
	$.ajax({
		type: "POST",
		url: "/api/sidebar/"
	}).done(function(msg) {
		$('#sidebar').empty();
		response = $.parseJSON(msg);
		if (response['media'].length > 0)
			$('#sidebar').attr('style','display: block;');
		else
			$('#sidebar').attr('style','display: none;');
		$.each(response['media'], function() {
			if (this.percentile < 0)
				$('#sidebar').append(createSidebarElementError(this.id,this.display_name));
			else
				$('#sidebar').append(createSidebarElement(this.id,this.display_name,this.percentile));
		});
		setTimeout(function(){updateSidebar()},500);
	});
}

function createSidebarElementError(id,name) {
	var a = document.createElement("a");
	var li = document.createElement("li");
	var strong = document.createElement("strong");
	var strong2 = document.createElement("strong");
	var div = document.createElement("div");
	var div2 = document.createElement("div");
	
	a.setAttribute('href','/media/');
	a.setAttribute('style','color: #FFF;');
	strong.innerHTML = name;
	strong2.setAttribute('class','pull-right');
	strong2.innerHTML = "ERROR";
	div.setAttribute('class','progress progress-danger slim');
	div2.setAttribute('class','bar');
	div2.setAttribute('data-percentage',100);
	div2.setAttribute('style','width: 100%');
	
	div.appendChild(div2);
	a.appendChild(strong);
	a.appendChild(strong2);
	a.appendChild(div);
	li.appendChild(a);
	
	return li;
}

function createSidebarElement(id,name,percentile) {
	var a = document.createElement("a");
	var li = document.createElement("li");
	var strong = document.createElement("strong");
	var strong2 = document.createElement("strong");
	var div = document.createElement("div");
	var div2 = document.createElement("div");
	
	a.setAttribute('href','/media/');
	a.setAttribute('style','color: #FFF;');
	strong.innerHTML = name;
	strong2.setAttribute('class','pull-right');
	strong2.innerHTML = percentile + "%";
	div.setAttribute('class','progress progress-info slim');
	div2.setAttribute('class','bar');
	div2.setAttribute('data-percentage',percentile);
	div2.setAttribute('style','width: ' + percentile + '%');
	
	div.appendChild(div2);
	a.appendChild(strong);
	a.appendChild(strong2);
	a.appendChild(div);
	li.appendChild(a);
	
	return li;
}

function deleteMedia(id) {
	$.ajax({
		type: "POST",
		url: "/api/delete/" + id
	});
	
	$('#media-row-' + id).remove();
}

function subscribe(name) {
	$.ajax({
		type: "POST",
		url: "/api/subscribe/" + name
	});
	
	$('#subscribeButton').attr('class','btn btn-success');
	$('#subscribeButton').attr('onclick','unsubscribe(\'' + name + '\')');
	$('#subscribeButton').html('Subscribed');
}

function unsubscribe(name) {
	$.ajax({
		type: "POST",
		url: "/api/unsubscribe/" + name
	});
	
	$('#subscribeButton').attr('class','btn btn-info');
	$('#subscribeButton').attr('onclick','subscribe(\'' + name + '\')');
	$('#subscribeButton').html('Subscribe');
}

function doack(id) {
	$.ajax({
		type: "POST",
		url: "/api/ack/" + id
	});
	if ((typeof ($('#ack-' + id).contents()[1]) !== 'undefined') && ($('#ack-' + id).contents()[1] !== null))
		$('#ack-' + id).contents()[1].setAttribute('class','icon-eye-open');
	$('#ack-' + id).attr('onclick','undoack(' + id + ')');
}

function undoack(id) {
	$.ajax({
		type: "POST",
		url: "/api/unack/" + id
	});
	if ((typeof ($('#ack-' + id).contents()[1]) !== 'undefined') && ($('#ack-' + id).contents()[1] !== null))
		$('#ack-' + id).contents()[1].setAttribute('class','icon-eye-close');
	$('#ack-' + id).attr('onclick','doack(' + id + ')');
}

function resizeCustom() {
	if (window.innerWidth<1024) {
		$('#leftColumn').attr('style','');
		$('#rightColumn').attr('style','');
		$('#mainImage').attr('style','margin-left: auto; margin-right: auto; display: block; max-width: ' + Math.min((window.innerWidth-80),300) + 'px;');
		$('#rightFloat').attr('style','text-align: center;');
	} else {
		$('#leftColumn').attr('style','float: left;');
		$('#rightColumn').attr('style','margin-left: 320px;');
		$('#mainImage').attr('style','max-width: 300px;');
		$('#rightFloat').attr('style','float: right;');
	}
}

window.addEventListener('resize',function(e){
	resizeCustom();
});

updateSidebar();
resizeCustom();
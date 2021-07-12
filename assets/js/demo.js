$(document).ready(function() {

	$('#search_text_input').focus(function() {
		if(window.matchMedia( "(min-width: 800px)" ).matches) {
			$(this).animate({width: '250px'}, 500);
		}
	});
	$('#search_text_input').blur(function() {
        if(window.matchMedia( "(min-width: 800px)" ).matches) {
            $(this).animate({width: '100px'}, 500);
        }
    });

	$('.button_holder').on('click', function() {
		document.search_form.submit();
	});

	//Button for profile post
	$('#submit_profile_post').click(function(){
		
		$.ajax({
			type: "POST",
			url: "includes/handlers/ajax_submit_profile_post.php",
			data: $('form.profile_post').serialize(),
			success: function(msg) {
				$("#post_form").modal('hide');
				location.reload();
			},
			error: function() {
				alert('Failure');
			}
		});

	});

	$('.round-chart').easyPieChart({
		'scaleColor': false,
		'lineWidth': 20,
		'lineCap': 'butt',
		'barColor': '#6d5cae',
		'trackColor': '#e5e9ec',
		'size': 190
	});
	
});

$(document).click(function(e){

	if(e.target.class != "search_results" && e.target.id != "search_text_input") {

		$(".search_results").html("");
		$('.search_results_footer').html("");
		$('.search_results_footer').toggleClass("search_results_footer_empty");
		$('.search_results_footer').toggleClass("search_results_footer");
	}

	if(e.target.className != "dropdown_data_window") {

		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "border" : "0px", "height" : "0px"});
	}


});

function getUsers(value, user) {
	$.post("includes/handlers/ajax_friend_search.php", {query:value, userLoggedIn:user}, function(data) {
		$(".searches").html(data);
	});
}

function getFriends(value, user) {
	$.post("includes/handlers/ajax_mention_friend_search.php", {query:value, userLoggedIn:user}, function(data) {
		$(".mentionees").html(data);
	});
}

function getFriendss(value, user) {
	$.post("includes/handlers/ajax_tag_friend_search.php", {query:value, userLoggedIn:user}, function(data) {
		$(".mentiones").html(data);
	});
}

function getFriendz(value, id) {
	$.post("includes/handlers/ajax_forward_friend_search.php", {query:value, post_id:id}, function(data) {
		$(".mentionees"+id).html(data);
	});
}

function getChatsSearch(value, userL, user){
	$.post("includes/handlers/ajax_chat_searches.php", {query:value, userLoggedIn:userL, username:user}, function(data) {
		$(".searches").html(data);
	});
}

function getChats(value, user) {
	document.getElementById("searchez").style.display="block";
	document.getElementById("onliners").style.display="none";
	if(value == ""){
		document.getElementById("loaded").style.display="block";
	}
	$.post("includes/handlers/ajax_chat_search.php", {query:value, userLoggedIn:user}, function(data) {
		$(".searchez").html(data);
	});
}

function textBlast(user) {
	document.getElementById("searchez").style.display="block";
	$.post("includes/handlers/ajax_chat_tb.php", {userLoggedIn:user}, function(data) {
		$(".searchez").html(data);
	});
}

function getTbs(value, user) {
	$.post("includes/handlers/ajax_get_tb.php", {query:value, userLoggedIn:user}, function(data) {
		$(".searchez").html(data);
	});
}

function getLiveUsers(value, user) {
	$.post("includes/handlers/ajax_get_users.php", {query:value, userLoggedIn:user}, function(data) {
		$("#searchesz").html(data);
	});
}

function getUserz(value, user, userL) {
	$.post("includes/handlers/ajax_friend_searches.php", {query:value, username:user, userLoggedIn:userL}, function(data) {
		$(".searches").html(data);
	});
}

function getGroupees(value, userL, userTo) {
	$.post("includes/handlers/ajax_gfriend_searches.php", {query:value, userLoggedIn:userL, username:userTo}, function(data) {
		$(".searches").html(data);
	});
}

function getLikers(value, id) {
	$.post("includes/handlers/ajax_like_searches.php", {query:value, post_id:id}, function(data) {
		$(".searches_likes").html(data);
	});
}

function like(id){
	$.post("includes/handlers/ajax_like.php", {post_id:id}, function(data) {
		$(".like_box"+id).html(data);
	});
	$.post("includes/handlers/ajax_like_statement.php", {post_id:id}, function(data) {
		$(".statement"+id).html(data);
	});
}

function c_like(id){
	$.post("includes/handlers/ajax_comment_like.php", {comment_id:id}, function(data) {
		$(".com"+id).html(data);
	});
}

function unlike(id){
	$.post("includes/handlers/ajax_unlike.php", {post_id:id}, function(data) {
		$(".like_box"+id).html(data);
	});
	$.post("includes/handlers/ajax_like_statement.php", {post_id:id}, function(data) {
		$(".statement"+id).html(data);
	});
}

function c_unlike(id){
	$.post("includes/handlers/ajax_comment_unlike.php", {comment_id:id}, function(data) {
		$(".com"+id).html(data);
	});
}

function acceptRequest(user, userL, dec){
	$.post("includes/handlers/ajax_accept_request.php", {username:user, userLoggedIn:userL, decision:dec}, function(data) {
		$(".request"+user).html(data);
	});
}

function ignoreRequest(user, userL){
	$.post("includes/handlers/ajax_ignore_request.php", {username:user, userLoggedIn:userL}, function(data) {
		$(".request"+user).html(data);
	});
}

function unfriend(user, userL){
	$.post("includes/handlers/ajax_unfriend.php", {username:user, userLoggedIn:userL}, function(data) {
		$(".btn"+user).html(data);
	});
}

function sendRequest(user, userL){
	$.post("includes/handlers/ajax_send_request.php", {username:user, userLoggedIn:userL}, function(data) {
		$(".suggestion"+user).html(data);
	});
}

function addFriend(user, userL){
	$.post("includes/handlers/ajax_send_request2.php", {username:user, userLoggedIn:userL}, function(data) {
		$(".btn"+user).html(data);
	});
}

function declineRequest(user, userL){
	$.post("includes/handlers/ajax_decline_request.php", {username:user, userLoggedIn:userL}, function(data) {
		$(".suggestion"+user).html(data);
	});
}

function typing(value, user, userL) {
	$.post("includes/handlers/ajax_typing.php", {query:value, username:user, userLoggedIn:userL}, function(data) {
		// $(".last_seen").html(data);
	});
}

function infolise(id){
	$.post("message.php", {msg_id:id}, function(data){
		$(".message_column").html(data);
	});
}

function forward(id){
	$.post("forward.php", {msg_id:id}, function(data){
		$(".message_column").html(data);
	});
}

function medSend(name){
	$.post("medsend.php", {username:name}, function(data){
		$(".message_column").html(data);
	});
}

function image(id, num){
	$.post("images.php", {post_id:id, number:num}, function(data){
		$(".modal"+id).html(data);
	});
}

function msg_image(id, num){
	$.post("image.php", {msg_id:id, number:num}, function(data){
		$(".modal"+id).html(data);
	});
}

function request(){
	$.post("requests.php", {}, function(data){
		$(".wrapper").html(data);
	});
}

function notifications(){
	$.post("notifications.php", {}, function(data){
		$(".wrapper").html(data);
	});
}

function searches(){
	$.post("searches.php", {}, function(data){
		$(".wrapper").html(data);
	});
}
function bookmarks(){
	$.post("bookmark.php", {}, function(data){
		$(".wrapper").html(data);
	});
}

function bring_back(name, userL){
	$.post("includes/handlers/ajax_get_messages.php", {username:name, userLoggedIn:userL}, function(data){
		$(".message_column").html(data);
	});
}

function pause(){
	var conts = document.getElementsByTagName('video');
	for(const vid of conts){
		vid.pause();
	}
}

function _(el){
	return document.getElementById(el);
}



function getDropdownData(user, type) {

	if($(".dropdown_data_window").css("height") == "0px") {

		var pageName;

		if(type == 'notification') {
			pageName = "ajax_load_notifications.php";
			$("span").remove("#unread_notification");
		}
		else if (type == 'message') {
			pageName = "ajax_load_messages.php";
			$("span").remove("#unread_message");
		}

		var ajaxreq = $.ajax({
			url: "includes/handlers/" + pageName,
			type: "POST",
			data: "page=1&userLoggedIn=" + user,
			cache: false,

			success: function(response) {
				$(".dropdown_data_window").html(response);
				$(".dropdown_data_window").css({"padding" : "0px", "height": "auto", "max-height": "280px" ,"border" : "1px solid #DADADA"});
				$("#dropdown_data_type").val(type);
			}

		});

	}
	else {
		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height": "0px", "border" : "none"});
	}

}

function getLiveSearchUsers(value, user) {

	$.post("includes/handlers/ajax_search.php", {query:value, userLoggedIn: user}, function(data) {

		if($(".search_results_footer_empty")[0]) {
			$(".search_results_footer_empty").toggleClass("search_results_footer");
			$(".search_results_footer_empty").toggleClass("search_results_footer_empty");
		}

		$('.search_results').html(data);
		$('.search_results_footer').html("<a href='search.php?q=" + value + "'>See All Results</a>");

		if(data == "") {
			$('.search_results_footer').html("");
			$('.search_results_footer').toggleClass("search_results_footer_empty");
			$('.search_results_footer').toggleClass("search_results_footer");
		}

	});

}


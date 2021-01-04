
var nametxt = $('.slot');
var phonetxt = $('.name');
var runing = true;
var trigger = true;
var inUser = (Math.floor(Math.random() * 10000)) % 5 + 1;
var num = 0;
$(function () {
	nametxt.css('background-image','url('+xinm[0]+')');
	phonetxt.html(phone[0]);
});

// 开始停止
function start() {

	if (runing) {

		if ( pcount <= Lotterynumber ) {
			alert("抽奖人数不足");
		}else{
			runing = false;
			$('#start').text('停止');
			startNum()
		}

	} else {
		$('#start').text('自动抽取中('+ Lotterynumber+')');
		zd();
	}
	
}

// 开始抽奖

function startLuck() {
	runing = false;
	$('#btntxt').removeClass('start').addClass('stop');
	startNum()
}

// 循环参加名单
function startNum() {
	num = Math.floor(Math.random() * pcount);
	nametxt.css('background-image','url('+xinm[num]+')');
	phonetxt.html(phone[num]);
	console.log(num);
	t = setTimeout(startNum, 0);
}

// 停止跳动
function stop() {
	pcount = xinm.length-1;
	clearInterval(t);
	t = 0;
}

// 打印中奖人

function zd() {
	console.log('抽奖开始');
	if (trigger) {

		trigger = false;
		var i = 0;

		if ( pcount >= Lotterynumber ) {
			stopTime = window.setInterval(function () {
				if (runing) {
					runing = false;
					$('#btntxt').removeClass('start').addClass('stop');
					startNum();
				} else {
					runing = true;
					$('#btntxt').removeClass('stop').addClass('start');
					stop();

					i++;
					Lotterynumber--;

					$('#start').text('自动抽取中('+ Lotterynumber+')');
					console.log(Lotterynumber);  //打印Lotterynumber

					if ( Lotterynumber <= 0 ) {
						console.log("抽奖结束");
						window.clearInterval(stopTime);
						$('#start').text("开始");
						trigger = true;
					};
						//打印中奖者名单
						$('.luck-user-list').prepend("<li><div class='portrait' style='background-image:url("+xinm[num]+")'></div><div class='luckuserName'>"+phone[num]+"</div></li>");
						$('.modality-list ul').append("<li><div class='luck-img' style='background-image:url("+xinm[num]+")'></div><p>"+phone[num]+"</p></li>");
						console.log('移除  '+'昵称：:'+phone[num]+'  头像地址：'+xinm[num]);
						//reluck(xinm[num]);
						//将已中奖者从数组中"删除",防止二次中奖
						xinm.splice($.inArray(xinm[num], xinm), 1);
						phone.splice($.inArray(phone[num], phone), 1);
					};
				//}
			},0);
		};
	}
}

function reluck(url){
	$.ajax({
		url:'lotterydeluser.php',
		type:'post',
		data:"headurl="+url,
		success:function(code){
			console.log(code);
		}
	});
}


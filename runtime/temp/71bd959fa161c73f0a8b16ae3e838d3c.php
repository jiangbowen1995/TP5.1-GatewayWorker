<?php /*a:1:{s:87:"D:\soft\environment\InstallDir\htdocs\tp51\application\api\view\v1\home\user\index.html";i:1571833650;}*/ ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="format-detection" content="telephone=no" />
    <title>沟通中</title>
    <link rel="stylesheet" type="text/css" href="/static/newcj/css/themes.css?v=2017129">
    <link rel="stylesheet" type="text/css" href="/static/newcj/css/h5app.css">
    <link rel="stylesheet" type="text/css" href="/static/newcj/fonts/iconfont.css?v=2016070717">
    <script src="/static/newcj/js/jquery.min2.js"></script>
    <script src="/static/newcj/js/dist/flexible/flexible_css.debug.js"></script>
    <script src="/static/newcj/js/dist/flexible/flexible.debug.js"></script>
    <script src="/static/qqFace/js/jquery.qqFace.js"></script>
    <style>
        .qqFace { margin-top: -180px; background: #fff; padding: 2px; border: 1px #dfe6f6 solid; }
        .qqFace table td { padding: 0px; }
        .qqFace table td img { cursor: pointer; border: 1px #fff solid; }
        .qqFace table td img:hover { border: 1px #0066cc solid; }
    </style>

</head>
<body ontouchstart>
<div class='fui-page-group'>
<div class='fui-page chatDetail-page'>
    <div class="chat-header flex">
        <i class="icon icon-toleft t-48" id="reback"></i>
        <span class="shop-titlte t-30">商店</span>
        <span class="shop-online t-26"></span>
        <span class="into-shop">进店</span>
    </div>
    <div class="fui-content navbar" style="padding:1.2rem 0 1.35rem 0;">
        <div class="chat-content">
            <p style="display: none;text-align: center;padding-top: 0.5rem" id="more"><a>加载更多</a></p>
            <p class="chat-time"><span class="time">2017-11-12</span></p>

            <!--<div class="chat-text section-left flex">-->
            <!--<span class="char-img" style="background-image: url(http://chat.com/static/newcj/img/123.jpg)"></span>-->
            <!--<span class="text"><i class="icon icon-sanjiao4 t-32"></i>你好</span>-->
        <!--</div>-->

            <!--<div class="chat-text section-right flex">-->
            <!--<span class="text"><i class="icon icon-sanjiao3 t-32"></i>你好</span>-->
            <!--<span class="char-img" style="background-image: url(http://chat.com/static/newcj/img/132.jpg)"></span>-->
           <!--</div>-->

        </div>
    </div>
    <div class="fix-send flex footer-bar">
        <i class="icon icon-emoji1 t-50"></i>
        <input class="send-input t-28" maxlength="200" id="saytext">
        <input type="file" name="pic" id="file" style="display: none">
        <i class="icon icon-add image_up t-50" style="color: #888;"></i>
        <span class="send-btn">发送</span>
    </div>
</div>
</div>

<script>

    var fromid = <?php echo htmlentities($fromid); ?>;

    var toid = <?php echo htmlentities($toid); ?>;
    var from_head = '';

    var to_head = '';

    var online = 0;
    var to_name='';
     var API_URL = "http://tp51.com/";
     var ws =  new WebSocket("wss://jiangbowen.cn/wss");
      ws.onmessage = function(e){
       var message =  eval("("+e.data+")");
          switch (message.type){
              case "init":
                   var bild = '{"type":"bind","fromid":"'+fromid+'"}';
                   ws.send(bild);
                   get_head(fromid,toid);
                   get_name(toid);
                   message_load();
                   var online = '{"type":"online","toid":"'+toid+'","fromid":"'+fromid+'"}';
                   ws.send(online);
                   changeNoRead();
                  return;
              case "text":
                      if(toid==message.fromid) {

                          $(".chat-content").append(' <div class="chat-text section-left flex"><span class="char-img" style="background-image: url('+to_head+')"></span> <span class="text"><i class="icon icon-sanjiao4 t-32"></i>' + replace_em(message.data) + '</span> </div>');

                          $(".chat-content").scrollTop(3000);

                          changeNoRead();
                      }
                  return;
              case "say_img":

                  $(".chat-content").append(' <div class="chat-text section-left flex"><span class="char-img" style="background-image: url('+to_head+')"></span> <span class="text"><i class="icon icon-sanjiao4 t-32"></i><img width="120em" height="120em" src="__ROOT__/uploads/'+message.img_name+'"></span> </div>');

                  $(".chat-content").scrollTop(3000);

                  changeNoRead();
                  return;
              case "save":
                  save_message(message);
                      if(message.isread==1){
                           online=1;
                          $(".into-shop").text("在线");
                      }else{
                           online=0;
                          $(".shop-online").text("不在线");
                      }
                  return;
              case  "online":
                  if(message.status==1){
                       online=1;
                      $(".shop-online").text("在线");

                  }else{
                     online=0;
                      $(".shop-online").text("不在线");

                  }


          }
    }

     $(".send-btn").click(function(){

         var text = $(".send-input").val();

         if(text==''){
             return;
         }

         var message = '{"data":"'+text+'","type":"say","fromid":"'+fromid+'","toid":"'+toid+'"}';

         $(".chat-content").append('<div class="chat-text section-right flex"><span class="text"><i class="icon icon-sanjiao3 t-32"></i>'+replace_em(text)+'</span> <span class="char-img" style="background-image: url('+from_head+')"></span> </div>');

         $(".chat-content").scrollTop(3000);

         ws.send(message);

         $(".send-input").val("");

     })


    function save_message(message){
        $.post(
                API_URL+"user/save_message" ,
                message,
                function(){

                },'json'
        )
    }

    function changeNoRead(){
        $.post(
                API_URL+"changeNoRead",
                {"fromid":fromid,"toid":toid},
                function(){

                },'json'
        )
    }

    function get_head(fromid,toid){
        $.post(
                API_URL+"user/get_head",
                {"fromid":fromid,"toid":toid},
                function(e){
                    console.log(e);
                    from_head = e.fromid;
                    to_head = e.toid;
                },'json'
        );
    }

    function  get_name(toid){
        $.post(
                API_URL+"user/get_name",
                {"uid":toid},
                function(e){
                    to_name = e.name;
                    $(".shop-titlte").text("与"+to_name+"聊天中...");
                    //console.log(e);
                },'json'
        );
    }

    function message_load(){
        $.post(
            API_URL +"load",
            {"fromid":fromid,"toid":toid},
            function(e){
                $.each(e,function(index,content){
                    if(fromid==content.from_id){
                        if(content.type==2){
                            $(".chat-content").append('<div class="chat-text section-right flex"><span class="text"><i class="icon icon-sanjiao3 t-32"></i><img width="120em" height="120em" src="/uploads/'+content.message+'"></span> <span class="char-img" style="background-image: url('+content.fpic+')"></span> </div>');

                        }else{
                            $(".chat-content").append('<div class="chat-text section-right flex"><span class="text"><i class="icon icon-sanjiao3 t-32"></i>'+replace_em(content.message)+'</span> <span class="char-img" style="background-image: url('+content.fpic+')"></span> </div>');
                        }
                    }else{
                        if(content.type==2){

                            $(".chat-content").append(' <div class="chat-text section-left flex"><span class="char-img" style="background-image: url('+content.tpic+')"></span> <span class="text"><i class="icon icon-sanjiao4 t-32"></i><img width="120em" height="120em" src="/uploads/'+content.message+'"></span> </div>');

                        }else{

                            $(".chat-content").append(' <div class="chat-text section-left flex"><span class="char-img" style="background-image: url('+content.tpic+')"></span> <span class="text"><i class="icon icon-sanjiao4 t-32"></i>'+replace_em(content.message)+'</span> </div>');
                        }
                    }
                })
                $(".chat-content").scrollTop(3000);
            },'json'
        );
    }




    $(function(){

        $('.icon-emoji1').qqFace({

            assign:'saytext',

            path:'/static/qqFace/arclist/'	//表情存放的路径

        });

        $(".sub_btn").click(function(){

            var str = $("#saytext").val();

            $("#show").html(replace_em(str));

        });

    });


    //查看结果

    function replace_em(str){

        // str = str.replace(/\</g,'&lt;');
        //
        // str = str.replace(/\>/g,'&gt;');
        //
        // str = str.replace(/\n/g,'<br/>');
        //
        // str = str.replace(/\[em_([0-9]*)\]/g,'<img src="/static/qqFace/arclist/$1.gif" border="0" />');

        return str;

    }


    $(".image_up").click(function(){
        $("#file").click();
    })

    $("#file").change(function(){

        formdata = new FormData();
        formdata.append('fromid',fromid);
        formdata.append('toid',toid);
        formdata.append('online',online);
        formdata.append('file',$("#file")[0].files[0]);

        $.ajax({
            url:API_URL+"uploadimg",
            type:'POST',
            cache:false,
            data:formdata,
            dataType:'json',
            processData:false,
            contentType:false,
            success:function(data,status,xhr){
                console.log(data);

                if(data.status=='ok'){

                    $(".chat-content").append('<div class="chat-text section-right flex"><span class="text"><i class="icon icon-sanjiao3 t-32"></i><img width="120em" height="120em" src="__ROOT__/uploads/'+data.img_name+'"></span> <span class="char-img" style="background-image: url('+from_head+')"></span> </div>');

                    $(".chat-content").scrollTop(3000);

                    var message = '{"data":"'+data.img_name+'","fromid":"'+fromid+'","toid":"'+toid+'","type":"say_img"}';

                    $("#file").val("");
                    ws.send(message);

                }else{
                    console.log(data.status);
                }



            }
        });



    })

    $('#reback').click(function(){
        window.history.back(-1);
    });
</script>
</body>
</html>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=gb2312;" />
    <meta name="Keywords" content="图片拖拽，图片扥等比缩放" />
    <meta name="Description" content="在页面上实现图片拖拽并可以随意调整图片大小。 但如果按住Ctrl键之后，当再调整图片大小的时候需要按照比例进行调整。" />
    <title>方寸运营后台管理系统</title>
    <meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <?php include $tpl . "/_head.php"; ?>
</head>
<body>
<style type="text/css">
    body {
        color: #eee;
        background-image: url("chrome://global/skin/media/imagedoc-darknoise.png");
    }
    .chr{cursor:e-resize;}
    .cvr{cursor:s-resize;}
    .cner{cursor:ne-resize;}
    .cnwr{cursor:nw-resize;}
    .cdft{cursor:default;}
    .cmove{cursor:move;}
</style>
<img src="<?= $pictureurl;?>" alt="" style="width:300px;position:absolute;left:400px;top:100px;" id="imgsrc" class="cdft" />
<div style="text-align: center;margin: 10px 0 0 0">
    <button style="width: 50%;" class="btn btn-success" onclick='turn()'>旋转(放大：Ctr组合+)(缩小：Ctr组合-)</button>
</div>
<script type="text/javascript">
    var img=document.getElementById('imgsrc');
    var span=10;
    var isDrag=null;
    var isIE=!!document.all;
    var ox,oy,ex,ey,ow,oh,chrPosX=false,chrPosY=false;
    var percent=img.offsetHeight/img.offsetWidth;
    function mouseMove(e){
        e=e||event;
        var x=e.offsetX||e.layerX,y=e.offsetY||e.layerY,imgW=img.offsetWidth,imgH=img.offsetHeight;
        if((x<=span&&y<=span)||(x>=imgW-span&&y>=imgH-span))img.className='cnwr';
        else if((x<=span&&y>=imgH-span)||(y<=span&&x>=imgW-span))img.className='cner';
        else if(x<=span||x>=imgW-span)img.className='chr';
        else if(y<=span||y>=imgH-span)img.className='cvr';
        else img.className='cdft';

    }
    function mouseDown(e){
        e=e||event;
        ex=e.clientX;//保存当前鼠标X轴的坐标
        ey=e.clientY;//保存当前鼠标y轴的坐标
        ox=parseInt(img.style.left);
        oy=parseFloat(img.style.top);
        if(img.className=='cdft'){
            isDrag=true;
            img.className='cmove';
        }
        else{
            isDrag=false;
            oh=img.offsetHeight;//获取对象相对于版面或由父坐标 offsetParent 属性指定的父坐标的高度
            ow=img.offsetWidth;
            var x=e.offsetX||e.layerX,y=e.offsetY||e.layerY;//相对容器的水平坐标,相对容器的垂直坐标
            chrPosX=x<=span;
            chrPosY=y<=span;
            if(e.ctrlKey){//事件属性可返回一个布尔值，指示当事件发生时，Ctrl 键是否被按下并保持住
                ow=oh/percent;//根据比例计算出宽度
                img.style.width=ow;//更新图片宽度
            }
        }
        if(isIE)img.setCapture();
        document.onmousemove=mouseDownAndMove;
        img.onmousemove=null;
        return false;
    }
    function mouseDownAndMove(e){
        e=e||event;
        if(isDrag===true){
            img.style.left=ox+e.clientX-ex+'px';
            img.style.top=oy+e.clientY-ey+'px';
        }
        else if(isDrag===false){
            var x=e.clientX-ex,y=e.clientY-ey;
            switch(img.className){
                case 'chr':
                    x=chrPosX?-x:x;
                    y=e.ctrlKey?percent*x+oh:oh;
                    img.style.width=ow+x+'px';
                    img.style.height=y+'px';
                    if(chrPosX)img.style.left=ox-x+'px';
                    break;
                case 'cvr':
                    y=chrPosY?-y:y;
                    x=e.ctrlKey?y/percent+ow:ow;
                    img.style.width=x+'px';
                    img.style.height=oh+y+'px';
                    if(chrPosY)img.style.top=oy-y+'px';
                    break;
                case 'cnwr':
                case 'cner':
                    x=chrPosX?-x:x;
                    if(e.ctrlKey){//按宽等比
                        y=e.ctrlKey?percent*x+oh:oh;
                        img.style.width=ow+x+'px';
                        img.style.height=y+'px';
                        if(chrPosX)img.style.left=ox-x+'px';
                    }
                    else{
                        y=chrPosY?-y:y;
                        img.style.width=ow+x+'px';
                        img.style.height=oh+y+'px';
                        if(chrPosX)img.style.left=ox-x+'px';
                        if(chrPosY)img.style.top=oy-y+'px';
                    }
                    break;
            }
        }
    }
    img.onmousemove=mouseMove;
    img.onmousedown=mouseDown;
    document.onmouseup=function(){
        if(typeof isDrag=="boolean"){
            if(isIE)img.releaseCapture();//函数的作用就是将后续的mouse事件都发送给这个对象
        }
        isDrag=null;
        img.className='cdft';
        img.onmousemove=mouseMove;
        document.onmousemove=null;
    }
</script>
<script>
    function rotate(id,angle,whence) {
        var p = document.getElementById(id);
        // we store the angle inside the image tag for persistence
        if (!whence) {
            p.angle = ((p.angle==undefined?0:p.angle) + angle) % 360;
        } else {
            p.angle = angle;
        }
        if (p.angle >= 0) {
            var rotation = Math.PI * p.angle / 180;
        } else {
            var rotation = Math.PI * (360+p.angle) / 180;
        }
        var costheta = Math.cos(rotation);
        var sintheta = Math.sin(rotation);
        if (document.all && !window.opera) {
            var canvas = document.createElement('img');
            canvas.src = p.src;
            canvas.height = p.height;
            canvas.width = p.width;
            canvas.style.filter = "progid:DXImageTransform.Microsoft.Matrix(M11="+costheta+",M12="+(-sintheta)+",M21="+sintheta+",M22="+costheta+",SizingMethod='auto expand')";
        } else {
            var canvas = document.createElement('canvas');
            if (!p.oImage) {
                canvas.oImage = new Image();
                canvas.oImage.src = p.src;
            } else {
                canvas.oImage = p.oImage;
            }
            canvas.style.width = canvas.width = Math.abs(costheta*canvas.oImage.width) +
                Math.abs(sintheta*canvas.oImage.height);
            canvas.style.height = canvas.height = Math.abs(costheta*canvas.oImage.height) +
                Math.abs(sintheta*canvas.oImage.width);
            var context = canvas.getContext('2d');
            context.save();
            if (rotation <= Math.PI/2) {
                context.translate(sintheta*canvas.oImage.height,0);
            } else if (rotation <= Math.PI) {
                context.translate(canvas.width,-costheta*canvas.oImage.height);
            } else if (rotation <= 1.5*Math.PI) {
                context.translate(-costheta*canvas.oImage.width,canvas.height);
            } else {
                context.translate(0,-sintheta*canvas.oImage.width);
            }
            context.rotate(rotation);
            context.drawImage(canvas.oImage, 0, 0, canvas.oImage.width, canvas.oImage.height);
            context.restore();
        }
        canvas.id = p.id;
        canvas.angle = p.angle;
        p.parentNode.replaceChild(canvas, p);
    }
    function rotateRight(id,angle) {
        rotate(id,angle==undefined?90:angle);
    }
    function rotateLeft(id,angle) {
        rotate(id,angle==undefined?-90:-angle);
    }
</script>
<script>
    var userAgent = navigator.userAgent,
        isIE = /msie/i.test(userAgent) && !window.opera,
        isWebKit = /webkit/i.test(userAgent),
        isFirefox = /firefox/i.test(userAgent);
    function rotate(target, degree) {
        if (isWebKit) {
            target.style.webkitTransform = "rotate(" + degree + "deg)";
        } else if (isFirefox) {
            target.style.MozTransform = "rotate(" + degree + "deg)";
        } else if (isIE) {
            degree = degree / 180 * Math.PI;
            var sinDeg = Math.sin(degree);
            var cosDeg = Math.cos(degree);

            target.style.filter = "progid:DXImageTransform.Microsoft.Matrix(" +
                "M11=" + cosDeg + ",M12=" + (-sinDeg) + ",M21=" + sinDeg + ",M22=" + cosDeg +

                ",SizingMethod='auto expand')";
        } else {
            target.style.transform = "rotate(" + degree + "deg)";
        }
    }
    var perNum=0;
    function turn()
    {
        perNum+=90;
        rotate(document.getElementById('imgsrc'), perNum);
    }
</script>
</body>
</html>
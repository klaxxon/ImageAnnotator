<html>
  <head>
    <title>Web PHP Annotator</title>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <style>
    .tclass {
      width: 100%;
      border: 1px solid black;
      cursor: pointer;
      font-size: 22px;
    }
    .title {
      font-size:2em;
    }
  </style>
  </head>
<body onload='init()' style="overflow-x:hidden;">
<table border='1' width="100%">
  <tr><td class='title'>PHP Annotator</td><td><div id='filename'></div></td></td></tr>
  <tr>
    <td style="width:1024px; height:768px;">
      <canvas id='canvas'></canvas>
    </td>
    <td valign='top' style='width:20%; text-align:center;'>
      Classes<br/>
      <div id='classes'>
      </div>
      <br/>
      <div id='debug'></div>
      <br/>
      <button onclick='undoLast()'>Undo Last Annotation</button><br/>
      <button onclick='clearAll()'>Clear Annotations</button><br/>
      <br/>
      <button onclick='save()'>Save</button><br/>
    </td>
  </tr>
</table>

<script>
var scale = 1;
var originx = 0;
var originy = 0;
var zoomIntensity = 0.2;
var tclass = null;
var classes = [];
var imagename = null;

var canvas = document.getElementById('canvas'),
    ctx = canvas.getContext('2d'),
    rect = {},
    img = new Image(),
    drag = false;


function class_click(cls) {
  console.log("Class changed to",cls);
  tclass = cls;
  $("#class_" + cls).prop("checked", true);
}

function clearAll() {
  classes = [];
  clear();
}

function undoLast() {
  classes.pop();
  clear();
}


function init() {
  classes = [];
  canvas.width = 1024;
  canvas.height = 768;
  scale = 1;
  originx = 0;
  originy = 0;
  $("#debug").html("");
 //ctx.fillStyle = "blue";
  //ctx.fillRect(0, 0, canvas.width, canvas.height);
  canvas.addEventListener('mousedown', mouseDown, false);
  canvas.addEventListener('mouseup', mouseUp, false);
  canvas.addEventListener('mousemove', mouseMove, false);
  $.getJSON( "process.php", {
    f: "load",
    format: "json"
  })
  .done(function( data ) {
    console.log(data);
    imagename = data.image;
    $("#filename").html(imagename);
    img.src = 'data/images/' + data.image;
    img.onload = function() {
      ctx.drawImage(img, 0, 0,1024,768);
    }
    col = 0;
    var r = '';
    for(var a in data.classes) {
      r += "<div class='tclass' style='background-color:hsl(" + col + ",100%,50%)' onclick='class_click(\"" + a+"\");'><input type=\"radio\" name='classes' id=\"class_" + a + "\"'/>" + data.classes[a]  + "</div>";
      col += 60;
    }
    $('#classes').html(r);
  });
}

function save() {
  $.post( "process.php", "f=save&image=" + imagename + "&classes="+JSON.stringify(classes), function( data ) {
    console.log(data);
    init();
  }, "json");
}


function mouseDown(e) {
  if (tclass == null) return;
  var rct = canvas.getBoundingClientRect();
  var mx = e.pageX - rct.left;
  var my = e.pageY - rct.top;
  rect.startX = mx / scale + originx;
  rect.startY = my / scale + originy;
  drag = true;
}

function mouseUp(e) {
  if (tclass == null) return;
  var rct = canvas.getBoundingClientRect();
  var mx = e.pageX - rct.left;
  var my = e.pageY - rct.top;
  var x2 = mx /scale + originx;
  var y2 = my / scale + originy;
  var z = {"class":tclass, "x1":(rect.startX/canvas.width), "y1":(rect.startY/canvas.height), "x2":(x2/canvas.width), "y2":(y2/canvas.height)};
  classes.push(z);
  //$("#debug").html(JSON.stringify(classes));
  console.log(classes);
  drag = false;
  clear();
}

function mouseMove(e) {
  if (drag) {
    var rct = canvas.getBoundingClientRect();
    var mx = e.pageX - rct.left;
    var my = e.pageY - rct.top;
    rect.w = mx / scale + originx - rect.startX;
    rect.h = my / scale + originy - rect.startY ;
    clear()
    draw();
  }
}

function draw() {
  ctx.setLineDash([6]);
  ctx.lineWidth = 5;
  ctx.fillStyle = "hsla(" + (tclass*60) + ", 100%, 50%, 0.3)";
  ctx.fillRect(rect.startX, rect.startY, rect.w, rect.h);
}

function clear() {
  ctx.drawImage(img, 0, 0,1024,768);
  for(var a in classes) {
    c = classes[a];
    ctx.fillStyle = "hsla(" + (c.class*60) + ", 100%, 50%, 0.3)";
    ctx.fillRect(c.x1*canvas.width, c.y1*canvas.height, (c.x2-c.x1)*canvas.width, (c.y2-c.y1)*canvas.height);
  }
}



canvas.onwheel = function(event) {
  event.preventDefault();

  var rct = canvas.getBoundingClientRect();
  var mousex = event.clientX - rct.left;
  var mousey = event.clientY - rct.top;
  
  var wheel = event.deltaY < 0 ? 1 : -1;

  var zoom = Math.exp(wheel*zoomIntensity);
  
  if (scale * zoom < 1.0) zoom = 1.0 / scale;
  
  var ox = originx - (mousex/(scale*zoom) - mousex/scale);
  var oy = originy - (mousey/(scale*zoom) - mousey/scale);
  
  if (ox < 0 || scale*zoom == 1.0) ox = 0;
  if (oy < 0 || scale*zoom == 1.0) oy = 0;

  ctx.translate(originx, originy);
  originx = ox;
  originy = oy;
  
  ctx.scale(zoom, zoom);
  ctx.translate(-originx, -originy);
  clear();

  scale *= zoom;
  visibleWidth = canvas.width / scale;
  visibleHeight = canvas.height / scale;
  console.log("scale", scale, "originx", originx, "originy", originy);
}

init();
</script>
</body>

</html>

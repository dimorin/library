function dragStart(ev) {
	ev.dataTransfer.effectAllowed='link';
	ev.dataTransfer.setData("id", ev.target.getAttribute('id'));
	 //ev.dataTransfer.setDragImage(ev.target,100,100);
	return true;
}
function dragEnter(ev) {
   event.preventDefault();
   return true;
}
function dragOver(ev) {
     event.preventDefault();
}
function dragDrop(ev) {
	ev.preventDefault ();
	var drag_id = ev.dataTransfer.getData("id");
	var drag_obj = document.getElementById(drag_id);
	var drag_obj_parent = drag_obj.parentNode;
	var tgt = ev.currentTarget.firstElementChild;
	//데이터가 존재하고 드래그 대상과 드롭 대상이 같지 않을 경우 드롭 실행
	if(drag_obj && drag_obj_parent != ev.currentTarget){
		
		 //드래그 대상에서 이동할 데이터 삭제
		drag_obj_parent.removeChild(drag_obj);
		 //드롭 대상에 데이터 추가
		ev.currentTarget.appendChild(drag_obj);
	}else if(drag_obj && tgt){
		ev.currentTarget.replaceChild (drag_obj, tgt);
		drag_obj_parent.appendChild (tgt);
	}else{
		// "onDrop fail"; 
	}
	//드롭 완료 후 이벤트 버블링을 막기 위해 호출     
	ev.stopPropagation();
   
   return false;
}

var num = 0;
function createSeat(e){
	var seats = document.getElementById("seats");
	// 새로운 div 엘리먼트를 만들고
	// 내용을 작성합니다.

	//var new_seat = document.createElement("div");
	//new_seat.className = "button";
	//new_seat.innerHTML = "drag me!";
	var new_seat = document.getElementById("temp").cloneNode(true);
	new_seat.id = num++;
	new_seat.innerHTML = "drag me!"+new_seat.id;
	// 생성된 엘리먼트를 추가합니다.	
	seats.appendChild (new_seat);
}

function createBlock(e){	
	var room = e.target.parentNode.nextSibling.nextSibling;
	console.log('block'+room);
	// 새로운 div 엘리먼트를 만들고
	// 내용을 작성합니다.

	//var new_seat = document.createElement("div");
	//new_seat.className = "button";
	//new_seat.innerHTML = "drag me!";
	var new_block = document.createElement("div");
	new_block.classList.add("block");
	new_block.classList.add("box");
	var resizer = document.createElement('div');
	resizer.className = 'resizer';
	new_block.appendChild(resizer);
	// 생성된 엘리먼트를 추가합니다.	
	room.appendChild(new_block);
	resizer.addEventListener('mousedown', initDrag, false);
}

function createRoom(e){
	var room_board = document.getElementById("room_board");
	// 새로운 div 엘리먼트를 만들고
	// 내용을 작성합니다.

	//var new_seat = document.createElement("div");
	//new_seat.className = "button";
	//new_seat.innerHTML = "drag me!";
	var new_room = document.getElementById("room_wrap_temp").cloneNode(true);
	new_room.innerHTML = "<div class='draggable_bar' onmousedown='_drag_init(this)'><h1>room"+Math.random()+"</h1></div><div class='room box is-pulled-left' ondragenter='return dragEnter(event)' ondrop='return dragDrop(event)' ondragover='return dragOver(event)'></div>";
	// 생성된 엘리먼트를 추가합니다.	
	room_board.appendChild (new_room);
}

function initDrag(e) {
	
   startX = e.clientX;
   startY = e.clientY;
   startWidth = parseInt(document.defaultView.getComputedStyle(e.target).width, 10);
   startHeight = parseInt(document.defaultView.getComputedStyle(e.target).height, 10);
   document.documentElement.addEventListener('mousemove', doDrag, false);
   document.documentElement.addEventListener('mouseup', stopDrag, false);
}

function doDrag(e) {
   e.target.style.width = (startWidth + e.clientX - startX) + 'px';
   e.target.style.height = (startHeight + e.clientY - startY) + 'px';
}

function stopDrag(e) {
    document.documentElement.removeEventListener('mousemove', doDrag, false);    document.documentElement.removeEventListener('mouseup', stopDrag, false);
}

var selected_bar = null, // Object of the element to be moved
x_pos_bar = 0, y_pos_bar = 0, // Stores x & y coordinates of the mouse pointer
x_elem_bar = 0, y_elem_bar = 0; // Stores top, left values (edge) of the element
window.onload = function(){	
    // Bind the functions...
    //document.getElementById("draggable_bar").onmousedown = function () {
		//_drag_init(this);
	   // return false;
	//};
	document.onmousemove = _move_elem;
	document.onmouseup = _destroy;


	var startX, startY, startWidth, startHeight;
}

// Will be called when user starts dragging an element
function _drag_init(elem) {
	
	// Store the object of the element which needs to be moved
    selected_bar = elem;
    x_elem_bar = x_pos_bar - selected_bar.offsetLeft;
    y_elem_bar = y_pos_bar - selected_bar.offsetTop;    
}

// Will be called when user dragging an element
function _move_elem(e) {
	x_pos_bar = document.all ? window.event.clientX : e.pageX;
    y_pos_bar = document.all ? window.event.clientY : e.pageY;
    if (selected_bar !== null) {
        //selected_bar.style.left = (x_pos_bar - x_elem_bar) + 'px';
        //selected_bar.style.top = (y_pos_bar - y_elem_bar) + 'px';
        selected_bar.parentNode.style.left = (x_pos_bar - x_elem_bar) + 'px';
        selected_bar.parentNode.style.top = (y_pos_bar - y_elem_bar) + 'px';
    }
}

// Destroy the object when we are done
function _destroy() {
	selected_bar = null;
}
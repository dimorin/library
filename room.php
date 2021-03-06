<!DOCTYPE html>

<html lang="en">
<head>
     <!-- The jQuery library is a prerequisite for all jqSuite products -->
    <script  type="text/javascript"  src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.min.js"></script>
    <!-- We support more than 40 localizations -->
    <script  type="text/javascript" src="jqgrid/grid.locale-kr.js"></script>
    <!-- This is the Javascript file of jqGrid -->   
    <script type="text/ecmascript" src="jqgrid/jquery.jqGrid.min.js"></script>
    <!-- A link to a Boostrap  and jqGrid Bootstrap CSS siles-->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> 
    <link rel="stylesheet" type="text/css" media="screen" href="jqgrid/ui.jqgrid-bootstrap.css" />
  <script>
    $.jgrid.defaults.width = 400;
    $.jgrid.defaults.responsive = true;
    $.jgrid.defaults.styleUI = 'Bootstrap';
  </script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <meta charset="utf-8" />
    <title>자리배치 세팅</title>
    <style>
    	
    	.unit_container:after{content:''; clear:both; display:block;}
    	.unit{float:left; display:inline-block; width:30px; height:30px; box-sizing:border-box; border:1px solid green; cursor:pointer;}
    	.unit:before, .unit:after{content:'';}
    	.able{position:relative; background:yellowgreen; opacity:1;}
    	.able:before{content:'d'; display:'inline-block'; position:absolute; right:0; bottom:0; font-size:9px;}
    	.unable{position:relative; background:yellowgreen; opacity:0.3;}
    	.unable:before{content:'no-d'; display:'inline-block'; position:absolute; right:0; bottom:0; font-size:9px;}
    	.clearfix{clear:both;}
    	.passage{opacity:0; border:0;}
    	input[readonly]{border:0;}
    	input[readonly].title{width:700px;}
    	
    </style>
</head>
<body>
	<? include "gnb.php"; ?>
	<div style="width:850px; margin:0 auto; padding:20px;">
		<div>
		    <table id="jqGrid"></table>
		    <div id="jqGridPager"></div>
		    <div id="detailsPlaceholder">
		        <table id="jqGridDetails"></table>
		        <div id="jqGridDetailsPager"></div>
		    </div>
		</div>
		<div style="border:1px solid #ddd; padding:10px; border-radius:3px; margin:20px 0;">
			<h4><input type="text" name="title" class="title" value="" readonly /> </h4>
			<fieldset>			    
			    <label>방 시작 번호 : <input type="text" name="startNumber" value="" readonly /></label><br> 
			    <label>행 : <input type="number" name="cols" value="4" /></label>, 
			    <label>열 : <input type="number" name="rows" value="4" /></label>
			</fieldset>
			<hr>
			<button class="init_seat">초기화</button>
			<button class="get_seat">자리에 번호 매기기</button>
			<button class="set_seat">자리 배치 정보 적용하기</button>
			<hr>
			<div class="unit_container">
				
			</div>			
		</div>
		<button id="getData" class="btn btn-default">GET DATA</button>	
    </div>
    <script type="text/javascript">
      	$(document).ready(function () {
      		var selected_row;
      		var selected_rowData;
      		var selected_startNumber;	


      		var template = "<div style='margin-left:15px;'><div> title<sup>*</sup>:</div><div> {title} </div>";
		        template += "<div> startNumber : </div><div>{startNumber} </div>";
		        template += "<hr style='width:100%;'/>";
		        template += "<div> {sData} {cData}  </div></div>";


      		$("#jqGrid").jqGrid({
                url: 'seat.json',
                editurl: 'clientArray',
                datatype: "json",
                colModel: [
                    { label: 'title', name: 'title', key: true, width: 120, editable: true,
              			editrules : { required: true} },
                    { label: 'startNumber', name: 'startNumber', width: 120, editable: true},
                    { label: 'map', name: 'map', width:400 }
                ],
                autowidth:true,
                height: 200,
                rowNum: 5,
				viewrecords: true,
				loadonce: true,
				caption: '열람실',
				sortname: 'title',
          		sortorder : 'asc',
                onSelectRow: function(rowid, selected) {                	
					if(rowid != null) {
						var rowObj = $(this).getRowData(rowid);
                		//console.log(rowObj);
						$('input[name=title]').val(rowObj.title+"의 자리 배치 세부 설정");
						$('input[name=startNumber]').val(rowObj.startNumber);
						selected_row = rowid;
						selected_rowData =rowObj;
						selected_startNumber = rowObj.startNumber;

						if(rowObj.map){
							$(".unit_container").html(rowObj.map);
						}else{
							fn_init_seat();
						}
					}					
				}, // use the onSelectRow that is triggered on row click to show a details grid
				onSortCol : updateView,
				onPaging : updateView,
                pager: "#jqGridPager"
            });	

      		$('#jqGrid').navGrid('#jqGridPager',
	          // the buttons to appear on the toolbar of the grid
	          { edit: true, add: true, del: true, search: true, refresh: true, view: true, position: "left", cloneToTop: false },
	          // options for the Edit Dialog
	          {
	            editCaption: "회원 정보 수정",
	            template: template,
	            errorTextFormat: function (data) {
	                return 'Error: ' + data.responseText
	            }
	          },
	          // options for the Add Dialog
	          {
	            caption:"회원 추가",
	            template: template,
	            errorTextFormat: function (data) {
	                return 'Error: ' + data.responseText
	            }
	          },
	          // options for the Delete Dailog
	          {
	            caption:"회원 삭제",
	            errorTextFormat: function (data) {
	                return 'Error: ' + data.responseText
	            }
	          });
	        


      		var $cols = $('input[name=cols]'), $rows = $('input[name=rows]');
      		var seat_value = [];
      		
      		

      		function updateView(){				
				seat_value=[];
				$(".unit_container").empty();
		    	colsNum = parseInt($cols.val() || 0, 10);
		    	rowsNum = parseInt($rows.val() || 0, 10);

		    	//console.log(colsNum, rowsNum);
		   		for(var i=0; i<colsNum; i++){
      				for(var j=0; j<rowsNum; j++){
      					//console.log(i,j);
      					var unit = (j === (0))? "<div class='unit able clearfix'><div class='seat_id'></div><div class='seat_host'></div></div>":"<div class='unit able'><div class='seat_id'></div><div class='seat_host'></div></div>";
      					seat_value.push(unit);
      				}
      			}

      			$(".unit_container").html(seat_value);

      			$(".unit").on("click",function(){//jqgrid 안에선 동작하지 않게 하려고 unit_wrap으로 감쌈
	      			//console.log('click');
	      			
	      			if($(this).hasClass("able")){
	      				$(this).removeClass("able");
	      				$(this).addClass("unable");
	      			}else if($(this).hasClass("unable")){
		      			$(this).removeClass("unable");
	      				$(this).addClass("passage");
	      			}else if($(this).hasClass("passage")){
		      			$(this).removeClass("passage");
	      				$(this).addClass("able");
	      			}

	      			
	      			/*
	      			if($(this).hasClass("able")){
	      				//console.log('able click');
	      				$(this).removeClass("able");
	      				$(this).addClass("unable");
	      			}else if($(this).hasClass("unable")){
		      			//console.log('unable click');
	      				$(this).removeClass("unable");
	      				$(this).addClass("able");
	      			}

	      			if($(this).hasClass("nonpassage")){
		      			//console.log('nonpassage click');
	      				$(this).removeClass("nonpassage");
	      				$(this).addClass("passage");
	      			}else if($(this).hasClass("passage")){
	      				//console.log('passage click');
	      				$(this).removeClass("passage");
	      				$(this).addClass("nonpassage");
	      			}
	      			*/
	      		});
			};
			updateView();      		

			

			function fn_init_seat(){
				$('input[name=cols]').val(4);
      			$('input[name=rows]').val(4);
				updateView();      			
			}
			/*
      		$(".fix_seat").on("click",function(){
      			$(".nonpassage").remove();
      		});
      		*/
      		$(".init_seat").on("click", fn_init_seat);
      		$(".get_seat").on("click", function(){
      			var num = selected_startNumber;
      			
      			var $div = $(".unit_container").find("div"); 
      			var unit_len = $div.length;

      			for(var i=0; i<unit_len; i++){
      				if($div.eq(i).hasClass("able")){
      					var count = num++;
      					$div.eq(i).attr('id', 'seat'+count);
      					$div.eq(i).find(".seat_id").html(count);
      				}
      				if($div.eq(i).hasClass("unable")){
      					$div.eq(i).attr('id', null);
      					$div.eq(i).find(".seat_id").remove();
      					$div.eq(i).find(".seat_host").remove();
      				}  
      				if($div.eq(i).hasClass("passage")){
      					$div.eq(i).attr('id', null);
      					$div.eq(i).find(".seat_id").remove();
      					$div.eq(i).find(".seat_host").remove();
      				}  
      							
      			}
      			alert($(".unit_container").html());
      			$(".unit").off("click");
      			/*
      			var map = [];
      			var $div = $(".unit_container").find("div"); 
      			var unit_len = $div.length;
      			for(var i=0; i<unit_len; i++){
      				var obj = [];
      				obj = $div.eq(i).attr('class').split(' ');
      				map[i]= obj;
      			}
      			console.log(map);
      			*/      			
      		});
      		$(".set_seat").on("click",function(){
      			selected_rowData.map = $(".unit_container").html();
      			$('#jqGrid').jqGrid('setRowData', selected_row, selected_rowData);
      		});
      		$('fieldset').on('input', 'input', function() {
		    	updateView();
		    	return false;
			});      		
			
      	});
		$("#getData").click(function(){
          var data = $('#jqGrid').getRowData();
          console.log(data);
          var myJsonString = JSON.stringify(data);
           //console.log(myJsonString);
          //console.log(JSON.parse(myJsonString));
        });
       
    </script>    
</body>
</html>
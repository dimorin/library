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
    <title>jqGrid Loading Data - Dialogs - Edit, Add, Delete</title>
</head>
<body>
	<? include "gnb.php"; ?>
	<div style="width:850px; margin:0 auto; padding:20px;">
		<div style="margin-left:20px">
			<table id="jqGrid"></table>
			<div id="jqGridPager"></div>
			<br>
			<button id="getData" class="btn btn-default">GET DATA</button>
		</div>
	</div>	
    <script type="text/javascript">
      $(document).ready(function () {
        var template = "<div style='margin-left:15px;'><div> member_id<sup>*</sup>:</div><div> {member_id} </div>";
        template += "<div> host: </div><div>{host} </div>";
        template += "<div> seat_id:</div><div> {seat_id} </div>";
        template += "<div> Phone: </div><div>{Phone} </div>";
        template += "<hr style='width:100%;'/>";
        template += "<div> {sData} {cData}  </div></div>";

        $("#jqGrid").jqGrid({
          url: 'member.json',
  // we set the changes to be made at client side using predefined word clientArray
          editurl: 'clientArray',
          datatype: "json",
          colModel: [
            {
              label: 'member_id',
              name: 'member_id',
              width: 100,
              key: true,
              editable: true,
              editrules : { required: true}
            },                            
            {
              label: 'host',
              name: 'host',
              width: 100,
              editable: true // must set editable to true if you want to make the field editable
            },
            {
              label: 'seat_id',
              name: 'seat_id',
              width: 80,
              editable: true
            },  
            {
              label : 'Phone',
              name: 'Phone',
              width: 100,
              editable: true
            }
          ],
          sortname: 'member_id',
          sortorder : 'asc',
          loadonce: true,
          viewrecords: true,
          width: 700,
          height: 380,
          rowNum: 10,
          pager: "#jqGridPager",
          caption: "member data" // set caption to any string you wish and it will appear on top of the grid
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
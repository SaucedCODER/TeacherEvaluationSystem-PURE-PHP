<?php include '../db_connect.php' ?>
<?php
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM faculty_list where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
}
?>
<div class="container-fluid">
	<div class="card card-widget widget-user shadow">
      <div class="widget-user-header bg-dark">
        <h3 class="widget-user-username"><?php echo ucwords($name) ?></h3>
        <h5 class="widget-user-desc"><?php echo $email ?></h5>
      </div>
      <div class="widget-user-image">
      	<?php if(empty($avatar) || (!empty($avatar) && !is_file('../assets/uploads/'.$avatar))): ?>
      	<span class="brand-image img-circle elevation-2 d-flex justify-content-center align-items-center bg-primary text-white font-weight-500" style="width: 90px;height:90px"><h4><?php echo strtoupper(substr($firstname, 0,1).substr($lastname, 0,1)) ?></h4></span>
      	<?php else: ?>
        <img class="img-circle elevation-2" src="assets/uploads/<?php echo $avatar ?>" alt="User Avatar"  style="width: 90px;height:90px;object-fit: cover">
      	<?php endif ?>
      </div>
      <div class="card-footer">
        <div class="container-fluid">
        	<dl>
        		<dt>Employee ID</dt>
        		<dd><?php echo $school_id ?></dd>

        	</dl>
			<!-- NEWLY added codes -->
			<div class="form-group">

<script>
	$(document).ready(() => {
		let checkList = document.getElementById('list1');
		checkList.getElementsByClassName('anchor')[0].onclick = function(evt) {
			if (checkList.classList.contains('visible'))
				checkList.classList.remove('visible');
			else
				checkList.classList.add('visible');
		}
	})
</script>
<style>
	.dropdown-check-list {
		display: inline-block;
	}

	.dropdown-check-list .anchor {
		position: relative;
		cursor: pointer;
		display: inline-block;
		padding: 5px 50px 5px 10px;
		border: 1px solid #ccc;
	}

	.dropdown-check-list .anchor:after {
		position: absolute;
		content: "";
		border-left: 2px solid black;
		border-top: 2px solid black;
		padding: 5px;
		right: 10px;
		top: 20%;
		-moz-transform: rotate(-135deg);
		-ms-transform: rotate(-135deg);
		-o-transform: rotate(-135deg);
		-webkit-transform: rotate(-135deg);
		transform: rotate(-135deg);
	}

	.dropdown-check-list .anchor:active:after {
		right: 8px;
		top: 21%;
	}

	.dropdown-check-list ul.items {
		padding: 2px;
		display: none;
		margin: 0;
		border: 1px solid #ccc;
		border-top: none;
	}

	.dropdown-check-list ul.items li {
		list-style: none;

	}

	.dropdown-check-list.visible .anchor {
		color: #0094ff;
	}

	.dropdown-check-list.visible .items {
		display: block;
	}
</style>

<div id="list1" class="dropdown-check-list" tabindex="100">
	<span class="anchor">Handled Subjects</span>
	<?php
	$qry = $conn->query("SELECT * FROM assignedsubjects t1,subject_list s1 WHERE t1.instructor_ID = $id and s1.id = t1.subject_ID");
	$nrows = $qry->num_rows;
	?>
	

	<ul class="items" id="unassignedsub-list">
	
		<?php 
		if($nrows > 0) { 
			 while ($row = $qry->fetch_assoc()) : ?>
			<?php echo "<li> " . $row['subject'] . " </li>" ?>

		<?php endwhile; }else {
		 echo "<li style='color:red;'> none </li>";


		}?>
	</ul>
</div>
</div>
        </div>
    </div>
	</div>
</div>
<div class="modal-footer display p-0 m-0">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
<style>
	#uni_modal .modal-footer{
		display: none
	}
	#uni_modal .modal-footer.display{
		display: flex
	}
</style>
<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_faculty"><i class="fa fa-plus"></i> Add New Faculty</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered table-sm" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Employee ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qryas = $conn->query("SELECT f1.id as fid FROM assignedsubjects t1,faculty_list f1, subject_list sl1
					WHERE   t1.subject_ID = sl1.id and t1.instructor_ID = f1.id; ");
					$array_qryas = [];
					if($qryas->num_rows > 0 ){
						while($row2 = $qryas->fetch_assoc()){ 
						array_push($array_qryas,array($row2["fid"]));
						}
					} 

					$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM faculty_list order by concat(firstname,' ',lastname) asc");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo $row['school_id'] ?></b></td>
						<td><b><?php echo ucwords($row['name']) ?></b></td>
						<td><b><?php echo $row['email'] ?></b></td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu">
		                      <a class="dropdown-item view_faculty" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=edit_faculty&id=<?php echo $row['id'] ?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <a class='dropdown-item delete_faculty' data-eat-wow='<?php
					if (isset($array_qryas)) {
						// print_r($array_qryas);
						$mer= 0;
						foreach ($array_qryas as $key => $value) {
						
							if($value[0] == $row['id']){
								$mer=1;
							}
								
						}
						if($mer == 1) echo "0";
						if($mer == 0) echo "1";

					}
					
					?>' title='Delete' href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
	$('.view_faculty').click(function(){
		uni_modal("<i class='fa fa-id-card'></i> Faculty Details",
		"<?php echo $_SESSION['login_view_folder'] ?>view_faculty.php?id="+$(this).attr('data-id'))
	})
	$('.delete_faculty').click(function(e){
		let wow = e.currentTarget.dataset.eatWow;
		if(wow === '0') {
			console.log('wow');
			_conf2(' Instructors that is currently assigned to a subject cannot be deleted' , 'Notice') 
		}else{
			_conf('Are you sure to delete this faculty?','delete_faculty',[$(this).attr('data-id')])
		}
		
			
					
					
	
	})
		$('#list').dataTable()
	})
	function delete_faculty($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_faculty',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>


<style>
   :root {
           --red:red;
           --black:black;
                        }
.color-red {
    width: 14px;
    height: 14px;
    background-color: var(--red);
    border-radius: 2px;
}
.box {
    display: flex;
    align-items: center;
  
}
.color-black {
    width: 14px;
    height: 14px;
    background-color: var(--black);
    border-radius: 2px;
}
.notice01 {
	display: flex;
}
</style>
<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		 <div class="card-header">
			<div class="notice01">
			Notice:<i> 
            <div class="box">
          <div class="color-black"></div>
         <div style="margin-left: 5px;"> = Regular</div>
         </div>
            <div class="box">
         <div class="color-red"></div>
         <div style="margin-left: 5px;"> = Irregular</div>
          </div> </i>
		 </div>
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_student"><i class="fa fa-plus"></i> Add New Student</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered table-sm" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Student ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Current Class</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$class= array();
					$classes = $conn->query("SELECT id,concat(curriculum,' ',level,' - ',section) as `class` FROM class_list");
					while($row=$classes->fetch_assoc()){
						$class[$row['id']] = $row['class'];
					}
					$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM student_list order by concat(firstname,' ',lastname) asc");
					while($row= $qry->fetch_assoc()):
					?>
					<tr <?php
						if ($row['Status'] != 'Regular' && $row['Status'] != null) {
							echo "style='color: var(--red);font-style:italic;' title='Irregular student'"; }
							else  {
								echo "style='color: var(--black);font-style:;' title='Regular student'";
							}?>>		   					       <th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo $row['school_id'] ?></b></td>
						<td 						
						><b><?php echo ucwords($row['name']) ?></b></td>
						<td><b><?php echo $row['email'] ?></b></td>

	
						<td><b><?php echo isset($class[$row['class_id']]) ? $class[$row['class_id']] : "N/A" ?></b></td>
					
				

					
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_student" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=edit_student&id=<?php echo $row['id'] ?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item delete_student" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
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
	$('.view_student').click(function(){
		uni_modal("<i class='fa fa-id-card'></i> student Details","<?php echo $_SESSION['login_view_folder'] ?>view_student.php?id="+$(this).attr('data-id'))
	})
	$('.delete_student').click(function(){
	_conf("Are you sure to delete this student?","delete_student",[$(this).attr('data-id')])
	})
		$('#list').dataTable()
	})
	function delete_student($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_student',
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
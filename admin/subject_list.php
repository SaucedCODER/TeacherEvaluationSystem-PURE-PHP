<?php include 'db_connect.php' ?>
<style>
						/* Tooltip container */
						.tooltip1 {
							position: relative;
							display: inline-block;
							
							z-index: 1;
						}

						/* Tooltip text */
						.tooltip1 .tooltiptext2 {
							visibility: hidden !important;
							width: 120px;
							background-color: black;
							color: #fff;
							text-align: center;
							padding: 5px 0;
							border-radius: 6px;

							/* Position the tooltip text - see examples below! */
							position: absolute;
							z-index: 2;
						}

						/* Show the tooltip text when you mouse over the tooltip container */
						.tooltip1:hover .tooltiptext2 {

							visibility: visible !important;
							margin-left: 10px;
							
						}
                        :root {
                            --red:red;
                            --black:black;
                        }
					.redbox {
    display: flex;
    align-items: center;
  
}
.colorred {
    width: 14px;
    height: 14px;
    background-color: var(--black);
    border-radius: 2px;
    align-items:center;
}
.limebox {
    display: flex;
    align-items: center;
  
}
.colorlime {
    width: 14px;
    height: 14px;
    background-color: var(--red);
    border-radius: 2px;
    align-items:center;
}
.notice01 {
	display: flex;
}
					</style>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
		<div class="notice01">
			Notice:<i class = "limebox" style = "margin: 0 5px;"> <span class = "colorlime"></span>  (Subjects that are currently assigned to a teacher),</i > <i class = "redbox" style = "margin: 0 5px;"> <span class = "colorred" ></span> (Subjects that are not yet assigned to a teacher).</i>
		 </div>
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_subject" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table table-sm table-hover table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="30%">
					<col width="40%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Code</th>
						<th>Subject</th>
						<th>Description</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					

					<?php
					$i = 1;
					$qryas = $conn->query("SELECT sl1.id as subid, concat(firstname,' ',lastname) as name FROM assignedsubjects t1,faculty_list f1, subject_list sl1
					WHERE   t1.subject_ID = sl1.id and t1.instructor_ID = f1.id; ");
					$array_qryas = [];
					if($qryas->num_rows > 0 ){
						while($row2 = $qryas->fetch_assoc()){ 
						array_push($array_qryas,array($row2["subid"],$row2['name']));
						}
					} 

					$qry = $conn->query("SELECT * FROM  subject_list ");
					while ($row = $qry->fetch_assoc()) :
					?>
						<tr  style='color: <?php
									if (isset($array_qryas)) {
										// print_r($array_qryas);
										$mer= 0;
										foreach ($array_qryas as $key => $value) {
										
											if($value[0] == $row['id']){
												$mer=1;
											}
												
										}
										if($mer == 1)echo 'var(--red)';

										if($mer == 0) echo 'var(--black)';

									}
									
										?>;'  >
							<th class="text-center"><?php echo $i++ ?></th>

							<td>
							
							<b><?php echo $row['code'] ?></b></td>

							<td  >
							<div class="tooltip1" ><b>
							
								<?php echo $row['subject'] ?></b>
									<span class="tooltiptext2" style='z-index:80 !important;'> 
										<?php
									if (isset($array_qryas)) {
										// print_r($array_qryas);
										$meronba= 0;
										foreach ($array_qryas as $key => $value) {
										
											if($value[0] == $row['id']){
												echo $value[1];
												$meronba=1;
											}
												
										}
										if($meronba == 0) echo "none";

									}
									
										?>
									</span>
								</div>
							</td>
							<td><b><?php echo $row['description'] ?></b></td>
							<td class="text-center">
								<div class="btn-group">
									<a href="javascript:void(0)" data-id='<?php echo $row['id'] ?>' class="btn btn-primary btn-flat manage_subject">
										<i class="fas fa-edit"></i>
									</a>
									
									<button <?php
									if (isset($array_qryas)) {
										// print_r($array_qryas);
										$mer= 0;
										foreach ($array_qryas as $key => $value) {
										
											if($value[0] == $row['id']){
												$mer=1;
											}
												
										}
										if($mer == 1) echo "disabled title='Subjects that is currently assigned to an instructor cannot be deleted'";

									}
									
										?> type="button" class="btn btn-danger btn-flat delete_subject" data-id="<?php echo $row['id'] ?>">
									
										<i class="fas fa-trash"></i>
									</button>
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





	
	$(document).ready(function() {
	
		$('.new_subject').click(function() {
			uni_modal("New subject", "<?php echo $_SESSION['login_view_folder'] ?>manage_subject.php")
		})
		$('.manage_subject').click(function() {
			uni_modal("Manage subject", "<?php echo $_SESSION['login_view_folder'] ?>manage_subject.php?id=" + $(this).attr('data-id'))
		})
		$('.delete_subject').click(function() {
			_conf("Are you sure to delete this subject?", "delete_subject", [$(this).attr('data-id')])
		})
		$('#list').dataTable()
	})

	function delete_subject($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_subject',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Data successfully deleted", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}
</script>
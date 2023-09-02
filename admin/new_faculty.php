<?php

?>
<div class="col-lg-12">
	<div class="card">
		<div class="card-body">
			<form action="" id="manage_faculty">
			
				<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
				<div class="row">
					<div class="col-md-6 border-right">
						<div class="form-group">
							<label for="" class="control-label">Employee ID</label>
							<input type="text" name="school_id" class="form-control form-control-sm" required value="<?php echo isset($school_id) ? $school_id : '' ?>">
						</div>
						<div class="form-group">
							<label for="" class="control-label">First Name</label>
							<input type="text" name="firstname" class="form-control form-control-sm" required value="<?php echo isset($firstname) ? $firstname : '' ?>">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Last Name</label>
							<input type="text" name="lastname" class="form-control form-control-sm" required value="<?php echo isset($lastname) ? $lastname : '' ?>">
						</div>
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
									height:22px;
									display:flex;
									align-items:center;

								}

								.dropdown-check-list.visible .anchor {
									color: #0094ff;
								}

								.dropdown-check-list.visible .items {
									display: block;
								}
								.dropdown-check-list label{
									font-weight:normal !important;
									margin:0;
									cursor: pointer;
								}
								.dropdown-check-list input{
									-ms-transform: scale(1.3); /* IE */
									-moz-transform: scale(1.3); /* FF */
									-webkit-transform: scale(1.3); /* Safari and Chrome */
									-o-transform: scale(1.3); /* Opera */
									transform: scale(1.3);
									padding: 10px;width:30px;
								}
							</style>

							<div id="list1" class="dropdown-check-list" tabindex="100">
								<span class="anchor">Assign Subjects</span>
								<?php
									$qry11 = $conn->query("SELECT * FROM academic_list WHERE is_default = 1");
									while ($row11 = $qry11->fetch_assoc()) : 
									
										

								$qry = $conn->query("SELECT  t1.*
							FROM    subject_list t1 LEFT JOIN
							assignedsubjects t2   ON  t1.id = t2.subject_ID
							WHERE   t2.subject_ID IS NULL ");
										$nrows1 = $qry->num_rows;
								
								?>
								<ul class="items" id="unassignedsub-list">
								<?php if ($nrows1 == 0) {
								 echo "<li style='color:red;'> none </li>";
								}?>
									<?php while ($row = $qry->fetch_assoc()) : ?>
										<?php echo "<li><input id='" . $row['subject'] . "11' style='cursor:pointer; 'name='subj[]'value='" . $row['id'] . "' type='checkbox' /><label for='" . $row['subject'] . "11' title='" . $row['subject'] . "'>" . $row['subject'] . "</label> </li>";?>

									<?php endwhile; 
										endwhile;
									
									?>

									<?php
								
									if (isset($id)) {
										$qry232 = $conn->query("SELECT s1.subject_id as rsid FROM restriction_list s1 UNION SELECT s2.subject_ids as rsid FROM restriction_list2 s2");
										$rsid = array();
										while ($row232 = $qry232->fetch_assoc()) : 
											$loopval = explode(",",$row232['rsid']);
											foreach ($loopval as $key => $value) {
											array_push($rsid,$value);

											}
										endwhile;


										$qry = $conn->query("SELECT *,s1.id as sid FROM assignedsubjects t1,subject_list s1 WHERE t1.instructor_ID = $id and s1.id = t1.subject_ID");
										 while ($row = $qry->fetch_assoc()) : 

											if (in_array($row['sid'], array_unique($rsid)) ) {
												echo "<li><input id='" . $row['subject'] . "11' style='cursor:pointer; 'name='subj[]'value='" . $row['id'] . "' type='checkbox' checked disabled title='Subject is currently included in the restriction list and cannot be unchecked!' />  <label for='" . $row['subject'] . "11'title='Subject is currently included in the restriction list and cannot be unchecked!'>" . $row['subject'] . "</label></li>";

												echo "<input name='subj[]'value='" . $row['id'] . "' type='hidden'  /> ";

											}else {
													echo "<li><input id='" . $row['subject'] . "11' style='cursor:pointer; 'name='subj[]'value='" . $row['id'] . "' type='checkbox' checked /><label for='" . $row['subject'] . "11' title='" . $row['subject'] . "'>" . $row['subject'] . "</label> </li>";

											}
											
										endwhile;
									}
								 ?>


								</ul>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Avatar</label>
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this,$(this))">
								<label class="custom-file-label" for="customFile">Choose file</label>
							</div>
						</div>
						<div class="form-group d-flex justify-content-center align-items-center">
							<img src="<?php echo isset($avatar) ? 'assets/uploads/' . $avatar : '' ?>" alt="Avatar" id="cimg" class="img-fluid img-thumbnail ">
						</div>
						<div class="form-group">
							<label class="control-label">Email</label>
							<input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($email) ? $email : '' ?>">
							<small id="#msg"></small>
						</div>
						<div class="form-group">
							<label class="control-label">Password</label>
							<input type="password" class="form-control form-control-sm" name="password" <?php echo !isset($id) ? "required" : '' ?>>
							<small><i><?php echo isset($id) ? "Leave this blank if you dont want to change you password" : '' ?></i></small>
						</div>
						<div class="form-group">
							<label class="label control-label">Confirm Password</label>
							<input type="password" class="form-control form-control-sm" name="cpass" <?php echo !isset($id) ? 'required' : '' ?>>
							<small id="pass_match" data-status=''></small>
						</div>
					</div>
				</div>
				<hr>
				<div class="col-lg-12 text-right justify-content-center d-flex">
					<button class="btn btn-primary mr-2">Save</button>
					<button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=faculty_list'">Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>
<style>
	img#cimg {
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	$('[name="password"],[name="cpass"]').keyup(function() {
		var pass = $('[name="password"]').val()
		var cpass = $('[name="cpass"]').val()
		if (cpass == '' || pass == '') {
			$('#pass_match').attr('data-status', '')
		} else {
			if (cpass == pass) {
				$('#pass_match').attr('data-status', '1').html('<i class="text-success">Password Matched.</i>')
			} else {
				$('#pass_match').attr('data-status', '2').html('<i class="text-danger">Password does not match.</i>')
			}
		}
	})

	function displayImg(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#cimg').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
	$('#manage_faculty').submit(function(e) {
		e.preventDefault()
		$('input').removeClass("border-danger")
		start_load()
		$('#msg').html('')
		if ($('[name="password"]').val() != '' && $('[name="cpass"]').val() != '') {
			if ($('#pass_match').attr('data-status') != 1) {
				if ($("[name='password']").val() != '') {
					$('[name="password"],[name="cpass"]').addClass("border-danger")
					end_load()
					return false;
				}
			}
		}
		$.ajax({
			url: 'ajax.php?action=save_faculty',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			success: function(resp) {
				if (resp == 1) {
					alert_toast('Data successfully saved.', "success");
					setTimeout(function() {
						location.replace('index.php?page=faculty_list')
					}, 750)
				} else if (resp == 2) {
					$('#msg').html("<div class='alert alert-danger'>Email already exist.</div>");
					$('[name="email"]').addClass("border-danger")
					end_load()
				} else if (resp == 3) {
					$('#msg').html("<div class='alert alert-danger'>Employee ID already exist.</div>");
					$('[name="school_id"]').addClass("border-danger")
					end_load()
				}
			}
		})
	})
</script>
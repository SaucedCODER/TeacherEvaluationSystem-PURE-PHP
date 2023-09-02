<?php
include '../db_connect.php';

?>
<style>

	.vscomp-toggle-button{
		box-shadow:none !important;
	}
	.vscomp-value{
		font-size:.875rem !important;
		font-family: inherit !important;
	}
	#thistd div{
		display:none;
	}
	#thistd div.active-s-l{
		display:table-row;
	}

	.make-it-maroon{
		color:maroon !important;
	}
   .vscomp-options-container {
         font-size:18px !important;
       height:100%;
        min-height:200px !important;
    }
    
  .vscomp-options-list {
            height:0px !important;
    }
    
</style>
<div class="container-fluid">
	<form action="" id="manage-restriction"  >
		<div class="row">
			<div class="col-md-4 border-right" >
				
				<input type="hidden" name="academic_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
				<div id="msg" class="form-group"></div>

				<div class="form-group">
					<label for="" class="control-label">Subject</label><br>
					<select id="multi_option" multiple name="native-select" placeholder="Please select here" data-silent-initial-value-set="true">
				
						<?php 
						$subject = $conn->query("SELECT sl1.id as asid, sl1.subject as subjname, sl1.id as subid,f1.id as faid, concat(firstname,' ',lastname) as name 
						FROM assignedsubjects t1,faculty_list f1, subject_list sl1
						 WHERE t1.subject_ID = sl1.id and t1.instructor_ID = f1.id;");
						$asid_arr = array();
						while($row=$subject->fetch_assoc()):
							$asid_arr[$row['asid']]= $row;

						?>
						<option value="<?php echo $row['asid'] ?>" <?php echo isset($subject_id) && $subject_id == $row['subid'] ? "selected" : "" ?>>
						<?php echo $row['subjname']." | ".$row['name'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="" class="control-label">Irregular Student</label>
					<select name="" id="cslll" class="form-control form-control-sm select2" >
						<option value=""></option>
						<?php 
						$allsubj = $conn->query("SELECT id,subject, code from subject_list ;  ");
						$allsubjarr = array();
						while($row17=$allsubj->fetch_assoc()):
							$allsubjarr[$row17['id']]= $row17;
						endwhile; 
							$slist[$row['slid']]= $row;
						$ireg = $conn->query("SELECT sl.id as slid,cl.id as clid,concat(firstname,' ',lastname) as name,concat(curriculum,' ',level,' - ',section) as class FROM student_list as sl,class_list as cl where sl.status = 'Irregular' and cl.id = sl.class_id ");
						$slist = array();
						while($row=$ireg->fetch_assoc()):
							
							$slist[$row['slid']]= $row;

						?>
						<option value="<?php echo $row['clid'].",".$row['slid'] ?>" <?php echo isset($student_id) && $student_id == $row['id'] ? "selected" : "" ?>>
						<?php echo ucwords($row['name'])." | ".ucwords($row['class']) ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<div class="d-flex w-100 justify-content-center">
						<button class="btn btn-sm btn-flat btn-primary bg-gradient-primary" id="add_to_list"type="button">Add to List</button>
					</div>
				</div>
			</div>
			
			<div class="col-md-8">
				<table class="table table-condensed table-hover" id="r-list">
					<thead>
						<tr>
							<th>Student 
								& Class
							</th>
							
							<th style="text-align:start;"><i style="display:block;"></i>Subjects & Instructor</th>
							
							<th>Action</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$restriction = $conn->query("SELECT * FROM restriction_list2 where academic_id = {$_GET['id']} order by id asc");
						while($row=$restriction->fetch_assoc()):
						?>
						<tr >
							<td data-st-id='<?php echo $row['student_id']?>'>
								<b><?php 
								//student name && class name data 
								echo isset($slist[$row['student_id']])  ? $slist[$row['student_id']]['name']." | ".$slist[$row['student_id']]['class'] : '' ?></b>


								<input type="hidden" name="rid[]" value="<?php echo $row['id'] ?>">
								<input type="hidden" name="class_id[]" value="<?php echo $row['class_id'] ?>">
								<input type="hidden" name="student_id[]" value="<?php echo $row['student_id'] ?>">

							</td>
							<td class='text-left cursor-zoom-in' id="thistd" style="cursor: zoom-in;"title="<?php 
								$var1234 = explode(",",$row['subject_ids']);
								echo "Subjects & Instructor &#013;";
								foreach ($var1234 as $value) {

									echo array_key_exists($value, $asid_arr) ?  $asid_arr[$value]['subjname']." ".$asid_arr[$value]['name']." &#013;" : $allsubjarr[$value]['code']." - ".$allsubjarr[$value]['subject']." is unavailable! &#013;" ;
									//subject name && intructor name data
								}
							?>">
							
								
									
									<?php
								

									/*

									

									$var123 = explode(",",$row['subject_ids']);
									echo !empty($var123) ? count($var123) : 'no laman its a bug';
									echo " x Scope";
									
									*/
									?>
									<?php 
								$var1234 = explode(",",$row['subject_ids']);
								//echo "Subjects & Instructor &#013;";
								foreach ($var1234 as $value) {
									echo array_key_exists($value, $asid_arr) ? 
									 "<div data-sub-id='$value'><b><sup>".$asid_arr[$value]['subjname']."  </sup>   ".$asid_arr[$value]['name']."</b></div>" : '';
									//subject name && intructor name data
								}
							?>
								
								<input type="hidden" name="subject_ids[]" value="<?php echo $row['subject_ids'] ?>">
							</td>
						
					
							<td class="text-center">
								<button class="btn btn-sm btn-outline-danger" onclick="removetolist(event)" type="button" ><i class="fa fa-trash"></i></button>
							</td>
							<td><a href="#" onclick="loadmore(event)" style="text-transform:underline;">View More</a></td>
						</tr>
					<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</div>
<div class="warnings101"></div>
<script>
			

	function removetolist(e) {
				let curr_stid = e.currentTarget.parentElement.parentElement.children[0];
				let st_id = document.querySelectorAll('[data-st-id]');
				let curr_sid_el = curr_stid.nextElementSibling.querySelectorAll('[data-sub-id]');

				let curr_sid = Array.from(curr_sid_el).map(e=>{
					if (e.classList.contains('make-it-maroon')) {
					return e.dataset.subId;
						
					}
				})
				let array_z = [] ,array_d = [] ;
				let subj_id2 = Array.from(st_id).map(e2=>{
							if (curr_stid.dataset.stId == e2.dataset.stId) {
								
							return e2.nextElementSibling.querySelectorAll('[data-sub-id]')

							}

						})	
				
						subj_id2.forEach(e2=>{
										if (e2 != undefined) {

											e2.forEach(e=>{
												if (curr_sid.includes(e.dataset.subId)){
													array_z.push(e.dataset.subId)
													array_d.push(e)
												} 
											})
										}
									})
								


						let vava = array_z.reduce((prev, curr) => (prev[curr] = ++prev[curr] || 1, prev), [])
					console.log('array_d ',array_d);
						vava.forEach((e,i)=>{
							if(e == 2){
								console.log('count', e);
								console.log('sid', i);

								array_d.forEach(e1=>{
									if (e1.dataset.subId == i){
										e1.classList.remove('make-it-maroon')
										console.log('remove it',e , e1);
									}

								})
							}
						})


				e.currentTarget.closest('tr').remove();


			}

      function loadmore(e){
            e.preventDefault();
        let trchilds = e.currentTarget.parentElement.parentElement.children[1].children;
        let flag = e.currentTarget.textContent;
		console.log(trchilds);
            if (flag == 'View More') {
                console.log(Array.from(trchilds));
                Array.from(trchilds).forEach(e=>{
                    e.style.display ="block";
                })
                e.currentTarget.textContent= "View less";
               
            } else {
           
                Array.from(trchilds).forEach((e,i)=>{
                    e.style.display ="none";
                  if (i == 0 ) {
                    e.style=display = "block";
                  }
                })
                e.currentTarget.textContent= "View More";
         
            }
        };

	$(document).ready(function(){
		let table101 = document.querySelectorAll('#thistd');

table101.forEach(e => {
	 e.children[0].classList.add('active-s-l');
});

// vscomp-value
			//droplist multiselect
	VirtualSelect.init({ 
	  ele: '#multi_option', 
	  disableSelectAll: true,
	//   optionsSelectedText: 'Subjects selected',
	//   optionSelectedText: 'Subjects selected',
	  dropboxWidth: "450px",
	  optionHeight: "22px",
  position: 'auto',
  maxWidth: '499px',
	});
	
		$('.select2').select2({
		    placeholder:"Please select here",
		    width: "100%"
		  });

		$('#manage-restriction').submit(function(e){
			e.preventDefault();
			let all_subids= document.querySelectorAll('[data-sub-id]')
			let flagy = true
			all_subids.forEach(e=>{
				if(e.classList.contains("make-it-maroon")){
					flagy = false
				}
			})
			
			if(flagy){
					start_load()
				$('#msg').html('')
				$.ajax({
					url:'ajax.php?action=save_restriction2',
					method:'POST',
					data:$(this).serialize(),
					success:function(resp){
					
						console.log(resp);
						
						// if (resp == 3) {
						// 	end_load()
						// 	alert("The Restriction table is empty. ");
							
						// 		location.reload()	
							
						// }
						if(resp == 1){
							alert_toast("Data successfully saved.","success");

							setTimeout(function(){
								location.reload()	
							},1750)
						}else if(resp == 2){
							$('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Class already exist.</div>')
							end_load()
						}
					}
				})
			}else{
				const elll = document.createElement('div')
				elll.classList.add('alert','alert-warning','alert-dismissible','fade','show')
				elll.role = 'alert'
				elll.style.csstext = `position:fixed;top:3rem;right:5rem;` 
				elll.innerHTML = `
Cannot save data because duplicates were found in the input. Please remove the duplicate entries and try again.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
`;
			document.querySelector('.warnings101').append(elll)
			setTimeout(() => {
				$('.alert').alert('close')
				document.querySelector('.warnings101').innerHtml = '';
			}, 4000);
			}
			
		})
	
		$('#add_to_list').click(function(){
			start_load()
			var frm = $('#manage-restriction')
			var cs = frm.find('#cslll').val()
			var sid = frm.find('#multi_option').val()

			let arr = cs.split(',');
			let gg = arr[1]//student id
			console.log("sid", sid);// sbject id
		
			if ( sid != "" && cs != "") {
			var s_arr = <?php echo json_encode($slist) ?>;
				console.log("arr",arr);

			var asid_arr = <?php echo json_encode($asid_arr) ?>;
			console.log("asid_arr",asid_arr);

			var tr = $("<tr class='cursor-pointer'></tr>")

				let sub_ins = ''
				let sub_ins2 = ''

				
				let st_id = document.querySelectorAll('[data-st-id]');
				let subj_id2 = Array.from(st_id).map(e2=>{
							if (gg == e2.dataset.stId) {
							return e2.nextElementSibling.querySelectorAll('[data-sub-id]')

							}

						})	
			sid.forEach(e1 => {	
			
				let htmll = `<div data-sub-id='${e1}'><sup>${asid_arr[e1].subjname}  </sup>   ${asid_arr[e1].name}</div>`;
				console.log(e1);

					subj_id2.forEach(e2=>{
						if (e2 != undefined) {
							e2.forEach(e=>{
								if (e.dataset.subId == e1){

									e.classList.add('make-it-maroon');
								htmll = `<div class='make-it-maroon' data-sub-id='${e1}'><sup>${asid_arr[e1].subjname}  </sup>   ${asid_arr[e1].name}</div>`
								} 
							})
						}
					})
				
					sub_ins += htmll;
					sub_ins2 += `${asid_arr[e1].subjname} | ${asid_arr[e1].name} &#013;`
				});
			
			

			tr.append(`<td data-st-id='${gg}'><b>${s_arr[gg].name} | ${s_arr[gg].class}</b><input type="hidden" name="rid[]" value=""><input type="hidden" name="class_id[]" value="${s_arr[gg].clid}"><input type="hidden" name="student_id[]" value="${s_arr[gg].slid}"></td>`)
			// tr.append('<td><b>'+sub_ins+ '</b></td>')
			tr.append(`<td class="text-left cursor-zoom-in" style="cursor: zoom-in;" title="Subjects & Instructor &#013;${sub_ins2}"><b >${sub_ins} </b><input type="hidden" name="subject_ids[]" value="${sid}"></td>`)

			tr.append('<td class="text-center disabled"><span class="btn btn-sm btn-outline-danger" onclick="removetolist(event)" type="button"><i class="fa fa-trash"></i></span></td>')
			tr.append('<td class="text-left" style="color:rgb(51, 204, 0);"><i>Newly added</i></td>')
			$('#r-list tbody').append(tr)
			frm.find('#cslll').val('').trigger('change')
			document.querySelector('#multi_option').reset();
			}else{
$('#msg').html('<div class="alert alert-danger" style="font-size:.8rem;"> <i class="fa fa-exclamation-triangle"></i> Please fill out all fields! <div>')

				setTimeout(function(){
$('#msg').html('')
						},1750)

			}
			end_load()
			
		})
	
		
	})

</script>
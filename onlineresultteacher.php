
<?php $faculty_id = $_SESSION['login_id'] ?>
<?php 
function ordinal_suffix($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}
?>
<div class="row">
		<div class="col-md-3">
			<div class="callout callout-info">
				<div class="list-group" id="class-list">
					
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="callout callout-info" id="printable">
			<div>
			<h3 class="text-center">Evaluation Report</h3>
			<hr>
			<table width="100%">
					<tr>
						<td width="50%"><p><b>Academic Year: <span id="ay"><?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?> Semester</span></b></p></td>
						<td></td>
					</tr>
					<tr>
						<!-- <td width="50%"><p><b>Class: <span id="classField"></span></b></p></td> -->
						<td width="50%"><p><b>Subject: <span id="subjectField"></span></b></p></td>
					</tr>
			</table>
				<p class=""><b>Total Student Evaluated: <span id="tse"></span></b></p>
			</div>
				<fieldset class="border border-info p-2 w-100">
				   <legend  class="w-auto">Overall Ratings</legend>
				   <table class="table table-condensed wborder"  >

							<tr class="bg-white">
								<th>5.Strongly-agree</th>
								<th>4.Agree </th>
								<th>3.Neutral </th>
								<th>2.Disagree </th>
								<th>1.Strongly-disagree </th>
							</tr>


							<tr class="bg-white" id="show-table" >

								<td id="strongly-agree">0%</td>
								<td id="agree">0%</td>
								<td id="uncertain">0%</td>
								<td id="disagree">0%</td>
								<td id="strongly-disagree">0%</td>
							</tr>

						</table>
				</fieldset>
				<?php 
							$q_arr = array();
						$criteria = $conn->query("SELECT * FROM criteria_list where id in (SELECT criteria_id FROM question_list where academic_id = {$_SESSION['academic']['id']} ) order by abs(order_by) asc ");
						while($crow = $criteria->fetch_assoc()):
					?>
					<table class="table table-condensed wborder" id='grand-parent'>
						<thead>
							<tr class="bg-gradient-secondary">
								<th class=" p-1"><b><?php echo $crow['criteria'] ?></b></th>
								<th width="5%" class="text-center">1</th>
								<th width="5%" class="text-center">2</th>
								<th width="5%" class="text-center">3</th>
								<th width="5%" class="text-center">4</th>
								<th width="5%" class="text-center">5</th>
							</tr>
							
						</thead>
						<tbody class="tr-sortable">
							<?php 
							$questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = {$_SESSION['academic']['id']} order by abs(order_by) asc ");
							while($row=$questions->fetch_assoc()):
							$q_arr[$row['id']] = $row;
							?>
							<tr class="bg-white">
								<td class="p-1" width="40%">
									<?php echo $row['question'] ?>
								</td>
								<?php for($c=1;$c<=5;$c++): ?>
								<td class="text-center">
									<span class="rate_<?php echo $c.'_'.$row['id'] ?> rates"></span>
			                      </div>
								</td>
								<?php endfor; ?>
							</tr>
							<?php endwhile; ?>
							<tr class="bg-white crit" style="font-weight: 900;"><td class="p-1" width="40%">Criteria Ratings</td><td class="text-center">0%</td><td class="text-center">0%</td><td class="text-center">0%</td><td class="text-center">0%</td><td class="text-center">0%</td></tr>
						</tbody>
					</table>
					<?php endwhile; ?>
			</div>
		</div>
	</div>
</div>
<style>
	.list-group-item:hover{
		color: black !important;
		font-weight: 700 !important;
	}
</style>
<noscript>
	<style>
		table{
			width:100%;
			border-collapse: collapse;
		}
		table.wborder tr,table.wborder td,table.wborder th{
			border:1px solid gray;
			padding: 3px
		}
		table.wborder thead tr{
			background: #6c757d linear-gradient(180deg,#828a91,#6c757d) repeat-x!important;
    		color: #fff;
		}
		.text-center{
			text-align:center;
		} 
		.text-right{
			text-align:right;
		} 
		.text-left{
			text-align:left;
		} 
	</style>
</noscript>
<script>
	$(document).ready(function(){
		load_class()
	})
	function load_class(){
		start_load()
		$.ajax({
			url:"ajax.php?action=get_class",
			method:'POST',
			data:{fid:<?php echo $faculty_id ?>},
			error:function(err){
				console.log(err)
				alert_toast("An error occured",'error')
				end_load()
			},
			success:function(resp){
				
				if(resp){
					resp = JSON.parse(resp)

					if(Object.keys(resp).length <= 0 ){
						$('#class-list').html('<a href="javascript:void(0)" class="list-group-item list-group-item-action disabled">No data to be display.</a>')
					}else{
						$('#class-list').html('')
						Object.keys(resp).map(k=>{
						$('#class-list').append('<a href="javascript:void(0)" data-json=\''+JSON.stringify(resp[k])+'\' data-id="'+resp[k].id+'" class="list-group-item list-group-item-action show-result">'+resp[k].subj+'</a>')
						})

					}
				}
			},
			complete:function(){
				end_load()
				anchor_func()
				if('<?php echo isset($_GET['rid']) ?>' == 1){
					$('.show-result[data-id="<?php echo isset($_GET['rid']) ? $_GET['rid'] : '' ?>"]').trigger('click')
				}else{
					$('.show-result').first().trigger('click')
				}
			}
		})
	}
	function anchor_func(){
		$('.show-result').click(function(){
			var vars = [], hash;
			var data = $(this).attr('data-json')
				data = JSON.parse(data)
			var _href = location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for(var i = 0; i < _href.length; i++)
				{
					hash = _href[i].split('=');
					vars[hash[0]] = hash[1];
				}
			window.history.pushState({}, null, './index.php?page=result&rid='+data.id);
			load_report(<?php echo $faculty_id ?>,data.sid,data.id);
			$('#subjectField').text(data.subj)
			$('#classField').text(data.class)
			$('.show-result.active').removeClass('active')
			$(this).addClass('active')
		})
	}
	function load_report($faculty_id, $subject_id,$class_id){
		if($('#preloader2').length <= 0)
		start_load()
		$.ajax({
			url:'ajax.php?action=get_report',
			method:"POST",
			data:{faculty_id: $faculty_id,subject_id:$subject_id,class_id:$class_id},
			error:function(err){
				console.log(err)
				alert_toast("An Error Occured.","error");
				end_load()
			},
			success:function(resp){
				if(resp){
					const parsedResponse = JSON.parse(resp);
					let numoftse = parsedResponse.tse;
					let datass = parsedResponse.data;

					const sumMap = new Map();

					for (const key in datass) {
					for (const subkey in datass[key]) {
						const value = datass[key][subkey];
						const sum = sumMap.get(subkey) || 0;
						sumMap.set(subkey, sum + value);
					}
					}
					// Loop through each table and get the number of tr elements in each tbody
					let totalquestions=0
					const gp = document.querySelectorAll("#grand-parent")
					gp.forEach(table => {
					const tbodies = table.querySelector('tbody');
						const trs = tbodies.querySelectorAll('tr');
						totalquestions += (trs.length - 1)
					
					});

					// console.log(totalquestions);
					// Divide the value of sumMap into totalquestions
					sumMap.forEach((value, key) => {
						sumMap.set(key, parseFloat(value/ totalquestions).toFixed(2));
					});

					// console.log(me,'im meee!!');
					document.getElementById("strongly-agree").innerText =  `${sumMap.get('5')||0}%`;
					document.getElementById("agree").innerText = `${sumMap.get('4')|| 0}%`;
					document.getElementById("uncertain").innerText = `${sumMap.get('3')|| 0}%`;
					document.getElementById("disagree").innerText = `${sumMap.get('2')|| 0}%`;
					document.getElementById("strongly-disagree").innerText = `${sumMap.get('1')||0}%`;

					resp = JSON.parse(resp)
					if(Object.keys(resp).length <= 0){
						$('.rates').text('')
						$('#tse').text('')
						$('#print-btn').hide()
					}else{
						$('#print-btn').show()
						$('#tse').text(resp.tse)
						$('.rates').text('-')
						var data = resp.data
						Object.keys(data).map(q=>{
							Object.keys(data[q]).map(r=>{
								$('.rate_'+r+'_'+q).text(parseFloat(data[q][r].toFixed(2)) + '%')
							})
						})
						const pr = document.querySelectorAll('#grand-parent');
						let calcc = calculateRates(pr);
						pr.forEach((e,c) => {
							
							let grandParentRows = e.getElementsByTagName('tbody')[0];
							let critElement = grandParentRows.lastElementChild
							let critTds = critElement.children
							critTds.forEach((td,i) => {
								if(!td.classList.contains('p-1')){
									td.innerText = calcc[c][0][i] ? parseFloat(calcc[c][0][i]).toFixed(2)+'%': '0%';
								}
							});
						
						})

					}
					
				}
			},
			complete:function(){
				end_load()
			}
		})
	}
	$('#print-btn').click(function(){
		start_load()
		var ns =$('noscript').clone()
		var content = $('#printable').html()
		ns.append(content)
		var nw = window.open("Report","_blank","width=900,height=700")
		nw.document.write(ns.html())
		nw.document.close()
		nw.print()
		setTimeout(function(){
			nw.close()
			end_load()
		},750)
	})
	function calculateRates(grandParentTables) {
								let ratesArray = [];
								let totalrr = [];
								let newrateArray = [];
								let gpt = [];

								grandParentTables.forEach((e) => {
									let grandParentRows = e.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
									// const op = grandParentRows.length
									// console.log(op);
									for (let i = 0; i < grandParentRows.length -1 ; i++) {
										let rowRates = {};
										let rowCells = grandParentRows[i].getElementsByTagName('td');

										for (let j = 1; j < rowCells.length; j++) {
											let rate = rowCells[j].getElementsByTagName('span')[0].textContent 
											rowRates[j] = rate ;
										}
										ratesArray.push(rowRates);
									}
									totalrr.push(...ratesArray, 'split here!');
									ratesArray = [];
								});
								let totals = totalrr.reduce((accumulator, e) => {
									if (e === 'split here!') {
										accumulator.push([...newrateArray]);
										newrateArray = [];
									} else {
										newrateArray.push(e);
									}
									return accumulator;

								}, []).map((e) => {
									let newobjss = {};
									for (let i = 0; i < e.length; i++) {
										const obj = e[i];
										for (let key in obj) {
											if (obj.hasOwnProperty(key) && obj[key] !== '-') {
												if (!newobjss[key]) {
													newobjss[key] = 0;
												}
												newobjss[key] += parseFloat(obj[key]);
											}
										}
									}
									return [newobjss];
								});

								grandParentTables.forEach((e, i) => {
									let grandParentRows = e.getElementsByTagName('tbody')[0].getElementsByTagName('tr').length;
									// console.log(grandParentRows);


									for (let key in totals[i][0]) {
										// console.log(totals[i][0]);

										totals[i][0][key] = totals[i][0][key] / (grandParentRows-1);
									}


								})




								return totals;
							}
</script>
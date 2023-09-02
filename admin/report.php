<?php $faculty_id = isset($_GET['fid']) ? $_GET['fid'] : ''; ?>
<?php
function ordinal_suffix($num)
{
	$num = $num % 100; // protect against large numbers
	if ($num < 11 || $num > 13) {
		switch ($num % 10) {
			case 1:
				return $num . 'st';
			case 2:
				return $num . 'nd';
			case 3:
				return $num . 'rd';
		}
	}
	return $num . 'th';
}
?>
<div class="col-lg-12">
	<div class="callout callout-info">
		<div class="d-flex w-100 justify-content-center align-items-center">
			<label for="faculty">Select Faculty</label>
			<div class=" mx-2 col-md-4">
				<select name="" id="faculty_id" class="form-control form-control-sm select2">
					<option value=""></option>
					<?php
					$faculty = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM faculty_list order by concat(firstname,' ',lastname) asc");
					$f_arr = array();
					$fname = array();
					while ($row = $faculty->fetch_assoc()) :
						$f_arr[$row['id']] = $row;
						$fname[$row['id']] = ucwords($row['name']);
					?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($faculty_id) && $faculty_id == $row['id'] ? "selected" : "" ?>><?php echo ucwords($row['name']) ?></option>
					<?php endwhile; ?>
				</select>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12 mb-1">
			<div class="d-flex justify-content-end w-100">


				<button class="btn btn-sm btn-success bg-gradient-success" style="display:none;margin-right:5px" id="pdf-btn"><i class="fas fa-download"></i> Download PDF file</button>
				<button class="btn btn-sm btn-success bg-gradient-success" style="display:none" id="print-btn"><i class="fa fa-print"></i> Print</button>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			<div class="callout callout-info">
				<div class="list-group mushi" id="class-list">

				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="callout callout-info" id="printable">
				<div class="callout callout-info" id="Pdf-file">
					<div>
						<h3 class="text-center">Evaluation Report</h3>
						<hr>
						<table id='expo' width="100%">
							<tr>
								<td width="50%">
									<p><b>Faculty: <span id="fname"></span></b></p>
								</td>
								<td width="50%">
									<p><b>Academic Year: <span id="ay"><?php echo $_SESSION['academic']['year'] . ' ' . (ordinal_suffix($_SESSION['academic']['semester'])) ?> Semester</span></b></p>
								</td>
							</tr>
							<tr>
								<p class=""><b> <span id="ratings"></span></b></p>
							</tr>
							<tr>
								<!-- <td width="50%"><p><b>Class: <span id="classField"></span></b></p></td> -->
								<td width="50%">
									<p><b>Subject: <span id="subjectField"></span></b></p>
								</td>
							</tr>
						</table>
						<p class=""><b>Total Student Evaluated: <span id="tse"></span></b></p>
					</div>
					<fieldset class="border border-info p-2 w-100">
						<legend class="w-auto">Overall Ratings</legend>
						<table class="table table-condensed wborder">

							<tr class="bg-white">
								<th>5.Strongly-agree</th>
								<th>4.Agree </th>
								<th>3.Neutral </th>
								<th>2.Disagree </th>
								<th>1.Strongly-disagree </th>
							</tr>


							<tr class="bg-white" id="show-table">

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
					while ($crow = $criteria->fetch_assoc()) :
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

								// 	$q =  $conn->query("SELECT * FROM question_list");
								// 	$row = $q->fetch_assoc();
								//    echo $row['criteria_id'];

								$questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = {$_SESSION['academic']['id']} order by abs(order_by) asc ");
								while ($row = $questions->fetch_assoc()) :

									$q_arr[$row['id']] = $row;
								?>
									<tr class="bg-white">
										<td class="p-1" width="40%">
											<?php echo $row['question'] ?>
										</td>
										<?php for ($c = 1; $c <= 5; $c++) : ?>
											<td class="text-center">
												<span class="rate_<?php echo $c . '_' . $row['id'] ?> rates"></span>
				</div>
				</td>

			<?php endfor; ?>


		<?php endwhile; ?>
		<tr class="bg-white crit" style="font-weight: 900;">
			<td class="p-1" width="40%">Criteria Ratings</td>
			<td class="text-center">0%</td>
			<td class="text-center">0%</td>
			<td class="text-center">0%</td>
			<td class="text-center">0%</td>
			<td class="text-center">0%</td>
		</tr>
		</tbody>

		</table>
	<?php endwhile; ?>
			</div>
		</div>
	</div>
</div>
<style>
	.list-group-item:hover {
		color: black !important;
		font-weight: 700 !important;
	}
</style>
<noscript>
	<style>
		table {
			width: 100%;
			border-collapse: collapse;
		}

		table.wborder tr,
		table.wborder td,
		table.wborder th {
			border: 1px solid gray;
			padding: 3px
		}

		table.wborder thead tr {
			background: #6c757d linear-gradient(180deg, #828a91, #6c757d) repeat-x !important;
			color: #fff;
		}

		.text-center {
			text-align: center;
		}

		.text-right {
			text-align: right;
		}

		.text-left {
			text-align: left;
		}
	</style>
</noscript>
<script>
	$(document).ready(function() {


		$('#faculty_id').change(function() {
			if ($(this).val() > 0)
				window.history.pushState({}, null, './index.php?page=report&fid=' + $(this).val());
			load_class()

		})
		if ($('#faculty_id').val() > 0)
			load_class()



	})

	function load_class() {
		start_load()
		const gp = document.querySelectorAll("#grand-parent")
		updateOverallCriteria(gp)
		var fname = <?php echo json_encode($fname) ?>;
		$('#fname').text(fname[$('#faculty_id').val()])
		$.ajax({
			url: "ajax.php?action=get_class",
			method: 'POST',
			data: {
				fid: $('#faculty_id').val()
			},
			error: function(err) {
				console.log(err)
				alert_toast("An error occured", 'error')
				end_load()
			},
			success: function(resp) {


				if (resp) {
					resp = JSON.parse(resp)

					if (Object.keys(resp).length <= 0) {
						$('#class-list').html('<a href="javascript:void(0)" class="list-group-item list-group-item-action disabled">No data to be display.</a>')
						$('.rates').text('')
						$('#tse').text('')
						$('#show-table').hide()
						$('#print-btn').hide()
						$('#pdf-btn').hide()




					} else {
						$('#class-list').html('')
						Object.keys(resp).map(k => {
							$('#class-list').append('<a href="javascript:void(0)" data-json=\'' + JSON.stringify(resp[k]) + '\' data-id="' + resp[k].id + '" class="list-group-item list-group-item-action show-result">' + resp[k].subj + '</a>')
						})

					}
				}
			},
			complete: function() {
				end_load()
				anchor_func()
				if ('<?php echo isset($_GET['rid']) ?>' == 1) {
					$('.show-result[data-id="<?php echo isset($_GET['rid']) ? $_GET['rid'] : '' ?>"]').trigger('click')
				} else {
					$('.show-result').first().trigger('click')
				}
			}
		})
	}

	function anchor_func() {
		$('.show-result').click(function() {
			var vars = [],
				hash;
			var data = $(this).attr('data-json')

			data = JSON.parse(data)
			var _href = location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for (var i = 0; i < _href.length; i++) {
				hash = _href[i].split('=');
				vars[hash[0]] = hash[1];
			}
			window.history.pushState({}, null, './index.php?page=report&fid=' + vars.fid + '&rid=' + data.id);
			load_report(vars.fid, data.sid, data.id);
			$('#subjectField').text(data.subj)
			$('#classField').text(data.class)
			$('.show-result.active').removeClass('active')
			$(this).addClass('active')
		})

	}

	function load_report($faculty_id, $subject_id, $class_id) {

		if ($('#preloader2').length <= 0)
			start_load()
		$.ajax({
			url: 'ajax.php?action=get_report',
			method: "POST",
			data: {
				faculty_id: $faculty_id,
				subject_id: $subject_id
			},
			error: function(err) {
				console.log(err)
				alert_toast("An Error Occured.", "error");
				end_load()
			},
			success: function(resp) {

				if (resp) {
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
					// console.log( datass);
					// Loop through each table and get the number of tr elements in each tbody
					let totalquestions = 0
					const gp = document.querySelectorAll("#grand-parent")
					gp.forEach(table => {
						const tbodies = table.querySelector('tbody');
						const trs = tbodies.querySelectorAll('tr');
						totalquestions += (trs.length - 1)

					});
					// console.log(totalquestions);
					// Divide the value of sumMap into totalquestions
					sumMap.forEach((value, key) => {
						sumMap.set(key, parseFloat(value / totalquestions).toFixed(2));
					});
					// console.log(me,'im meee!!');
					document.getElementById("strongly-agree").innerText = `${sumMap.get('5')||0}%`;
					document.getElementById("agree").innerText = `${sumMap.get('4')|| 0}%`;
					document.getElementById("uncertain").innerText = `${sumMap.get('3')|| 0}%`;
					document.getElementById("disagree").innerText = `${sumMap.get('2')|| 0}%`;
					document.getElementById("strongly-disagree").innerText = `${sumMap.get('1')||0}%`;

					// console.log("get r : "+resp);
					// console.log('numberofTSE: '+numoftse);

					resp = JSON.parse(resp)
					if (Object.keys(resp).length == 0) {
						$('.rates').text('')
						$('#tse').text('')
						$('#print-btn').hide()
						$('#pdf-btn').hide()

					} else {
						$('#print-btn').show()
						$('#show-table').show()
						$('#tse').text(resp.tse)
						$('.rates').text('-')
						var data = resp.data
						console.log(resp, 'ako ay isang response!');
						Object.keys(data).map(q => {

							Object.keys(data[q]).map(r => {
								//number of categotres
								// console.log(`${r} =total`);
								// console.log(`${q} =total`);
								// console.log($('.rate_' + r + '_' + q), data[q][r])
								let dataqr = data[q][r]
								//percentage
								// console.log(dataqr);
								$('.rate_' + r + '_' + q).text(parseFloat(dataqr.toFixed(2)) + '%')
							})


						})

						//criteria overall
						DomManipulation_Criteria(gp, calculateRates)



					}

				}
			},
			complete: function() {
				end_load()
			}
		})
	}
	$('#print-btn').click(function() {
		start_load()
		var ns = $('noscript').clone()
		var content = $('#printable').html()
		ns.append(content)
		var nw = window.open("Report", "_blank", "width=900,height=700")
		nw.document.write(ns.html())
		nw.document.close()
		nw.print()
		setTimeout(function() {
			nw.close()
			end_load()
		}, 750)
	})

	const tablepdf = document.getElementById('pdftable');
	const pdfton = document.getElementById('pdf-btn')
	pdfton.addEventListener('click', function() {
		// Create a new jsPDF instance
		var doc = new jsPDF();
		// Convert the HTML table to a PDF
		doc.autoTable({
			html: '#pdftable'
		});

		// Attach the PDF to the download button
		pdfton.href = doc.output('dataurlstring');

	});
	// 	document.getElementById('pdf-btn').onclick = function() {
	// 		var containPDF = document.getElementById('Pdf-file');

	// 	 var customize = {
	// 		margin: .5, 
	// 		filename: 'evaluation.pdf',
	// 		html2canvas: {scale:2},
	// 		jsPDF: {unit:'in',format: 'letter',orientation:'portrait'}
	// 	 };

	// 	 html2pdf(containPDF, customize);
	// };
	function DomManipulation_Criteria(pr, calculateRates) {

		let calcc = calculateRates(pr);
		console.log(calcc, 'high im calcc');
		updateOverallCriteria(pr, calcc)
	}

	function updateOverallCriteria(pr, total = []) {
		pr.forEach((e, c) => {
			let grandParentRows = e.getElementsByTagName('tbody')[0];
			let critElement = grandParentRows.lastElementChild
			let critTds = critElement.children
			critTds.forEach((td, i) => {
				if (!td.classList.contains('p-1')) {

					if (total.length == 0) {
						td.innerText = '0%';
					} else {
						td.innerText = total[c][0][i] ? parseFloat(total[c][0][i]).toFixed(2) + '%' : '0%';
					}

				}
			});

		})
	}

	function calculateRates(grandParentTables) {
		let ratesArray = [];
		let totalrr = [];
		let newrateArray = [];
		let gpt = [];

		grandParentTables.forEach((e) => {
			let grandParentRows = e.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
			// const op = grandParentRows.length
			// console.log(op);
			console.log(grandParentRows[0], 'ee');
			for (let i = 0; i < grandParentRows.length - 1; i++) {
				let rowRates = {};
				let rowCells = grandParentRows[i].getElementsByTagName('td');

				for (let j = 1; j < rowCells.length; j++) {
					let rate = rowCells[j].getElementsByTagName('span')[0].textContent
					rowRates[j] = rate;
				}
				ratesArray.push(rowRates);
			}
			totalrr.push(...ratesArray, 'split here!');
			ratesArray = [];
		});
		console.log(totalrr, 'im totalrr');

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
				console.log(totals[i][0][key]);
				// console.log(totals[i][0]);

				totals[i][0][key] = totals[i][0][key] / (grandParentRows - 1);
			}


		})



		console.log(totals, ' totals here');

		return totals;
	}
</script>
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
    <span class="anchor">Assign Subjects</span>
    <?php
    $qry = $conn->query("SELECT  t1.*
FROM    subject_list t1 LEFT JOIN
assignedsubjects t2   ON  t1.id = t2.subject_ID
WHERE   t2.subject_ID IS NULL;");
    ?>
    <ul class="items" id="unassignedsub-list">
        <?php while ($row = $qry->fetch_assoc()) : ?>
            <?php echo "<li><input style='cursor:pointer; 'name='subj[]'value='" . $row['id'] . "' type='checkbox' /> " . $row['subject'] . " </li>" ?>

        <?php endwhile; ?>
    </ul>
</div>
</div>
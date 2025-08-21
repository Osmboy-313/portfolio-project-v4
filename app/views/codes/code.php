
<?php

// echo '<pre>';
// print_r($adminCodes);
// print_r($bossCodes);
// echo $totalRecords;
// echo '</pre>';

?>


<div class="title">

    <div class="tabs">
        <button data-tab-target="#admin" class="tab tab1 <?= $activeTab === '#admin' ? 'selected' : '' ?> ">Admin Codes</button>
        <button data-tab-target="#boss" class="tab tab2 <?= $activeTab === '#boss' ? 'selected' : '' ?> ">Boss Codes</button>
        <div class="active"></div>
    </div>

    <button 
        type="button" 
        class="btn btn--primary btn--add-code" 
        data-modal-target="#add-modal"
        > 
        Create Code 
    </button>

</div>


<div class="main-content tab-content codes"> </div>

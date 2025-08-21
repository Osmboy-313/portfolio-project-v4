<?php

require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/post.php';
require_once __DIR__ . '/../models/code.php';
require_once __DIR__ .  '/../core/view.php';
require_once __DIR__ .  '/../core/auth.php';

function code_index()
{
    // 🔐 Check if user is logged in and is a boss
    auth_require_user_type(['boss']);

    $activeTab = $_GET['tab'] ?? '#admin';
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $recordsPerPage = 10;
    $offset = ($currentPage - 1) * $recordsPerPage;

    $adminCodes = $bossCodes = [];
    $totalRecords = $totalPages = 1;

    switch ($activeTab) {
        case '#boss':
            $totalRecords = countBossCodes();
            $bossCodes = getBosscodes($recordsPerPage, $offset);
            break;
        default:
            $totalRecords = countAdminCodes();
            $adminCodes = getAdmincodes($recordsPerPage, $offset);
            break;
    }

    $totalPages = max(1, ceil($totalRecords / $recordsPerPage));

    $serialNumber = $offset + 1;

    $start = $offset + 1;
    $end = $offset + $recordsPerPage;
    $end = min($end, $totalRecords);


    if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {

        // Render only the partial view and exit
        echo view('codes/tabs', [
            'activeTab' => $activeTab,
            'adminCodes' => $adminCodes,
            'bossCodes' => $bossCodes,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'recordsPerPage' => $recordsPerPage,
            'serialNumber' => $serialNumber,
            'start' => $start,
            'end' => $end,
        ],);
        exit;
    }

    $modals = view('components/modals');

    echo view(
        'codes/code',
        [
            'title' => 'Codes',
            'modals' => $modals,
            'activeTab' => $activeTab,
            'adminCodes' => $adminCodes,
            'bossCodes' => $bossCodes,
            'totalRecords' => $totalRecords,
        ],
        'private'
    );
}

function code_add(){

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $data = json_decode(file_get_contents('php://input'),true);

        $response = [];

        $code = trim($data['code']);
        $column = $data['columnName'] ?? '';

        $check = doesCodeExist($column, $code);

        if (empty($code)) $response['error']['name'] = "Enter code !" ;
        if (!empty($code) && !empty($check)) $response['error']['name'] = "This Code Already Exists !" ;

        if(!isset($response['error'])){
            $addCode = addCode($column, $code);

            if($addCode){
                $response['success'] = "Successfully Added the Code !";
            }
            else{
                $response['failure'] = "Failed to the Code !";
            }
        }

        echo json_encode($response);
        
    }

}

function code_doesExist(){

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data = json_decode(file_get_contents('php://input'),true);

        $id = $data['id'] ?? 0;
        $code = trim($data['name']);
        $column = $data['column'] ?? '';
        if (empty($code)) return;

        $check = doesCodeExist($column, $code, $id);
        $exists = false;

        if (!empty($check)) {
            $exists = true;
        }

        echo json_encode(['exists' => $exists, 'id' => $id, 'name' => $code]);
    }

}

function code_getById(){

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data = json_decode(file_get_contents('php://input'),true);

        $codeId = trim($data['id']);
        $column = $data['column'] ??'';

        $codeRecord = getCodeById($codeId, $column);
        $code = $codeRecord['admin_code'] ?? $codeRecord['boss_code']; 

        echo json_encode(['code' => $code]);
    }

}

function code_edit(){
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $data = json_decode(file_get_contents('php://input'),true);

        $response = [];

        $id = $data['id'] ?? null;
        $code = trim($data['code']) ?? '';
        $column = $data['column'] ?? '';

        $doesCodeExists = doesCodeExist($column, $code, $id);
        $codeFromDb = getCodeById($id,$column);

        if(empty($code)) $response['error']['name'] = 'Enter Code !';
        if(!empty($code) && !empty($doesCodeExists)) $response['error']['name'] = 'This Code Already Exists !';

        if(!empty($code) && $code === $codeFromDb[$column]){
            $response['noChange'] = "You Changed nothing, you moron !";
        } 

        if(!isset($response['error']) && !isset($response['noChange']) ){
            
            $updateStatus = updateCode($id, $code, $column);
            
            if($updateStatus){
                $response['success'] = 'Successfully Updated the Code !';
            }
            else{
                $response['failure'] = 'Failed to Update the Code !';
            }

        }

        echo json_encode($response);

    }

}

function code_delete(){

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $data = json_decode(file_get_contents('php://input'),true);
        $response = [];

        $id = $data['id'] ?? 0;
        $column = $data['column'] ??'';

        $deleteCode = deleteCode( $id, $column );

        if($deleteCode){
            $response['success'] = 'Successfully deleted the Code !';
        }
        else{
            $response['failure'] = 'Failed to delete the Code !';
        }

        echo json_encode($response);
    }

}

function code_get(){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $codes = fetch_codes();
        echo json_encode($codes);
    }
}

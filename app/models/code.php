<?php

require_once __DIR__ . '/../core/db.php';

function fetch_codes()
{
    $conn = db();
    $sql = $conn->prepare("SELECT * FROM `codes`");
    $sql->execute();
    $result = $sql->get_result();

    if ($result && $result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

function addCode($codeColumn, $code){
    $conn = db();
    $sql = $conn->prepare("INSERT INTO `codes`($codeColumn) VALUES (?)");
    $sql->bind_param('s', $code);
    

    return $sql->execute();
}

function updateCode($id, $code, $column){
    $conn = db();
    $sql = $conn->prepare("UPDATE `codes` SET $column = ? WHERE id = ? ");
    $sql->bind_param('si', $code, $id);
    

    return $sql->execute();
}

function getAdmincodes($recordsPerPage, $offset)
{
    $conn = db();
    $sql = $conn->prepare("SELECT id, admin_code FROM codes WHERE admin_code IS NOT NULL ORDER BY id ASC  LIMIT ? OFFSET ?");
    $sql->bind_param('ii', $recordsPerPage, $offset);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}


function getBosscodes($recordsPerPage, $offset)
{
    $conn = db();
    $sql = $conn->prepare("SELECT id, boss_code FROM codes WHERE boss_code IS NOT NULL ORDER BY id ASC  LIMIT ? OFFSET ?");
    $sql->bind_param('ii', $recordsPerPage, $offset);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function countAdminCodes()
{
    $conn = db();
    $result = $conn->query("SELECT COUNT(*) AS total FROM codes WHERE admin_code IS NOT NULL");
    return $result->fetch_assoc()['total'];
}

function countBossCodes()
{
    $conn = db();
    $result = $conn->query("SELECT COUNT(*) AS total FROM codes WHERE boss_code IS NOT NULL");
    return $result->fetch_assoc()['total'];
}


function doesCodeExist($codeColumn, $code, $idToExclude = 0)
{
    $allowedColumns = ['admin_code', 'boss_code']; // whitelist columns
    if (!in_array($codeColumn, $allowedColumns)) {
        throw new Exception("Invalid column name.");
    }

    $conn = db();
    
    $sql = $conn->prepare("SELECT id, $codeColumn FROM codes WHERE $codeColumn IS NOT NULL AND $codeColumn = ? AND id != ?");

    $sql->bind_param('si', $code, $idToExclude);
    $sql->execute();
    $result = $sql->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getCodeById($id, $column){
    $conn = db();
    $sql = $conn->prepare("SELECT id,$column FROM codes WHERE id = ? ");
    $sql->bind_param('i',$id);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_assoc();
}

function deleteCode($id, $column){
    $conn = db();

    $sql = $conn->prepare("DELETE FROM `codes` WHERE id = ?");
    $sql->bind_param('i',$id);

    return $sql->execute();;
}
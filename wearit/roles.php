<?php

function isLoggedIn(){
    return isset($_SESSION['user']);
}

function isAdmin(){
    if(isLoggedIn()){
        if($_SESSION['user']['role'] === 'admin'){
            return true;
        }
    }
    return false;
}

function isUser() {
    if (isLoggedIn()) {
        if ($_SESSION['user']['role'] === 'user') {
            return true;
        }
    }
    return false;
}

function isGuest() {
    if (isLoggedIn()) {
        if ($_SESSION['user']['role'] === 'guest') {
            return true;
        }
    }
    return false;
}
?>
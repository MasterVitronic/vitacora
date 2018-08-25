<?php

if($auth->logOut()){
    header("Location: /", true, 301);
}

<?php
$secret_key = 'benx-technologies';
$nama = trim($_GET['nama']);
$password = trim($_GET['password']);
$pin = trim($_GET['pin']);
$serial = trim($_GET['serial']);
if(!isset($_GET['nama']) OR !isset($_GET['nama']) OR !isset($_GET['serial']) OR !isset($_GET['pin']) )
{
    $licensi = 'error';
}
else
{
    if($nama == 'BENX')
    {
        if($password == '07081964464')
        {
            $as = true;
        }else{
            $as = false;
        }
        if($pin == '2365'){
            $as = true;
        }else{
            $as = false;
        }
    }
    else
    {
        $as = false;
    }
    if($as == true)
    {
        $licensi = md5($nama.$password.$serial.$secret_key);
    }
    else
    {
        $licensi = 'error';
    }
}
echo $licensi;
?>
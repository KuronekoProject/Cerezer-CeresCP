<?php

////// CONFIGURAÇÕES //////
// Host SQL (IP):
$sql_host = "localhost";
//
// Usuário SQL:
$sql_user = "root";
//
// Senha SQL:
$sql_pass = "senha";
//
// Banco de Dados:
$sql_db = "ragnarok";
///////////////////////////

session_start();

if(!@mysql_connect($sql_host, $sql_user, $sql_pass))
die('Não foi possível conectar ao Banco de Dados, cheque as configurações.');

foreach($_POST AS $post => $valor)
if(!get_magic_quotes_gpc())
$_POST[$post] = addslashes($valor);

if(isset($_GET['deslogar']))
{
session_destroy();
header("Location: index.php");
}
else if(isset($_SESSION['auth']) && $_SESSION['auth'] == 1)
{
if(isset($_POST['act']) && $_POST['act'] == "CASH")
{
settype($_POST['cash'], "integer");
if($_POST['cash'])
{
$query = mysql_query("SELECT * FROM `{$sql_db}`.`login` WHERE `userid` = '{$_POST['user']}'");
$result = mysql_fetch_assoc($query);
if(!$result)
echo "Usuário inexistente.";
else
{
$query = mysql_query("SELECT * FROM `{$sql_db}`.`global_reg_value` WHERE `account_id` = '{$result['account_id']}' AND `str` = '#CASHPOINTS'");
$result2 = mysql_fetch_assoc($query);
if(!$result2)
mysql_query("INSERT INTO `{$sql_db}`.`global_reg_value` (`char_id`, `str`, `value`, `type`, `account_id`) VALUES ('0', '#CASHPOINTS', '{$_POST['cash']}', '2', '{$result['account_id']}')");
else
mysql_query("UPDATE `{$sql_db}`.`global_reg_value` SET `value` = `value`+{$_POST['cash']} WHERE `account_id` = '{$result['account_id']}'");
if(mysql_error())
echo "Ocorreu um erro durante a consulta: ".mysql_error();
else
echo "{$_POST['cash']} Cash Point's adicionados para {$_POST['user']} (AID: {$result['account_id']}).";
}
}
else
echo "Você deve inserir um valor numérico inteiro para o campo 'Quantidade de Cash'.";
echo "<br><br>-----------------------<br><br>";
}
echo "
<form action='index.php' method='POST'>
Login: <input name='user'/>
Quantidade de Cash: <input name='cash'/>
<input type='hidden' name='act' value='CASH'/>
<input type='submit'/>
</form>
<a href='index.php?deslogar'>Deslogar</a>
";
}
else
{
if(isset($_POST['act']) && $_POST['act'] == "LOGIN")
{
$query = mysql_query("SELECT * FROM `{$sql_db}`.`login` WHERE `userid` = '{$_POST['user']}' AND `user_pass` = '{$_POST['pass']}'");
$result = mysql_fetch_assoc($query);
if(!$result)
echo "Usuário/Senha inválido(s).";
if(isset($result['level']) && $result['level'] < 99)
echo "Você não pode acessar este painel.";
if($result && $result['level'] == 99)
{
$_SESSION['auth'] = 1;
header("Location: index.php");
}
echo "<br><br>-----------------------<br><br>";
}

mysql_close();

?>
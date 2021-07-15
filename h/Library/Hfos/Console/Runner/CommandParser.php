<?php

class CommandParser extends lime_parser {
var $qi = 0;
var $i = array (
  0 =>
  array (
    'statement' => 's 1',
    'grant_statement' => 's 2',
    'aura_statement' => 's 4',
    'tatico_statement' => 's 6',
    'auth_statement' => 's 8',
    'gc_statement' => 's 10',
    'restart_service_statement' => 's 12',
    'backup_statement' => 's 14',
    'show_statement' => 's 16',
    'PS' => 's 18',
    'WHOAMI' => 's 20',
    'DISK' => 's 22',
    'UPTIME' => 's 25',
    'DATE' => 's 27',
    'HELP' => 's 29',
    'AURA' => 's 31',
    'TATICO' => 's 34',
    'AUTH' => 's 36',
    'GRANT' => 's 38',
    'GC' => 's 57',
    'RESTART' => 's 59',
    'BACKUP' => 's 62',
    'SHOW' => 's 64',
    'command' => 's 67',
    '\'start\'' => 'a \'start\'',
  ),
  1 =>
  array (
    '#' => 'r 0',
  ),
  2 =>
  array (
    'EOL' => 's 3',
  ),
  3 =>
  array (
    '#' => 'r 1',
  ),
  4 =>
  array (
    'EOL' => 's 5',
  ),
  5 =>
  array (
    '#' => 'r 2',
  ),
  6 =>
  array (
    'EOL' => 's 7',
  ),
  7 =>
  array (
    '#' => 'r 3',
  ),
  8 =>
  array (
    'EOL' => 's 9',
  ),
  9 =>
  array (
    '#' => 'r 4',
  ),
  10 =>
  array (
    'EOL' => 's 11',
  ),
  11 =>
  array (
    '#' => 'r 5',
  ),
  12 =>
  array (
    'EOL' => 's 13',
  ),
  13 =>
  array (
    '#' => 'r 6',
  ),
  14 =>
  array (
    'EOL' => 's 15',
  ),
  15 =>
  array (
    '#' => 'r 7',
  ),
  16 =>
  array (
    'EOL' => 's 17',
  ),
  17 =>
  array (
    '#' => 'r 8',
  ),
  18 =>
  array (
    'EOL' => 's 19',
  ),
  19 =>
  array (
    '#' => 'r 9',
  ),
  20 =>
  array (
    'EOL' => 's 21',
  ),
  21 =>
  array (
    '#' => 'r 10',
  ),
  22 =>
  array (
    'USAGE' => 's 23',
  ),
  23 =>
  array (
    'EOL' => 's 24',
  ),
  24 =>
  array (
    '#' => 'r 11',
  ),
  25 =>
  array (
    'EOL' => 's 26',
  ),
  26 =>
  array (
    '#' => 'r 12',
  ),
  27 =>
  array (
    'EOL' => 's 28',
  ),
  28 =>
  array (
    '#' => 'r 13',
  ),
  29 =>
  array (
    'EOL' => 's 30',
  ),
  30 =>
  array (
    '#' => 'r 14',
  ),
  31 =>
  array (
    'var' => 's 32',
    'VARIABLE' => 's 33',
  ),
  32 =>
  array (
    'EOL' => 'r 15',
  ),
  33 =>
  array (
    'EOL' => 'r 33',
    'ON' => 'r 33',
    '\',\'' => 'r 33',
    'TO' => 'r 33',
    'VARIABLE' => 'r 33',
  ),
  34 =>
  array (
    'var' => 's 35',
    'VARIABLE' => 's 33',
  ),
  35 =>
  array (
    'EOL' => 'r 16',
  ),
  36 =>
  array (
    'var' => 's 37',
    'VARIABLE' => 's 33',
  ),
  37 =>
  array (
    'EOL' => 'r 17',
  ),
  38 =>
  array (
    'privilege_list' => 's 39',
    'privilege' => 's 56',
    'var' => 's 55',
    'VARIABLE' => 's 33',
  ),
  39 =>
  array (
    'ON' => 's 40',
    '\',\'' => 's 53',
  ),
  40 =>
  array (
    'COMPROBS' => 's 41',
  ),
  41 =>
  array (
    'comprob_list' => 's 42',
    'comprob' => 's 52',
    'var' => 's 50',
    'literal' => 's 51',
    'VARIABLE' => 's 33',
    'STRING' => 's 47',
  ),
  42 =>
  array (
    'TO' => 's 43',
    '\',\'' => 's 48',
  ),
  43 =>
  array (
    'username' => 's 44',
    'var' => 's 45',
    'literal' => 's 46',
    'VARIABLE' => 's 33',
    'STRING' => 's 47',
  ),
  44 =>
  array (
    'EOL' => 'r 18',
  ),
  45 =>
  array (
    'EOL' => 'r 31',
  ),
  46 =>
  array (
    'EOL' => 'r 32',
  ),
  47 =>
  array (
    'TO' => 'r 34',
    '\',\'' => 'r 34',
    'EOL' => 'r 34',
  ),
  48 =>
  array (
    'comprob' => 's 49',
    'var' => 's 50',
    'literal' => 's 51',
    'VARIABLE' => 's 33',
    'STRING' => 's 47',
  ),
  49 =>
  array (
    'TO' => 'r 27',
    '\',\'' => 'r 27',
  ),
  50 =>
  array (
    'TO' => 'r 29',
    '\',\'' => 'r 29',
  ),
  51 =>
  array (
    'TO' => 'r 30',
    '\',\'' => 'r 30',
  ),
  52 =>
  array (
    'TO' => 'r 28',
    '\',\'' => 'r 28',
  ),
  53 =>
  array (
    'privilege' => 's 54',
    'var' => 's 55',
    'VARIABLE' => 's 33',
  ),
  54 =>
  array (
    'ON' => 'r 24',
    '\',\'' => 'r 24',
  ),
  55 =>
  array (
    'ON' => 'r 26',
    '\',\'' => 'r 26',
  ),
  56 =>
  array (
    'ON' => 'r 25',
    '\',\'' => 'r 25',
  ),
  57 =>
  array (
    'var' => 's 58',
    'VARIABLE' => 's 33',
  ),
  58 =>
  array (
    'EOL' => 'r 19',
  ),
  59 =>
  array (
    'SERVICE' => 's 60',
  ),
  60 =>
  array (
    'var' => 's 61',
    'VARIABLE' => 's 33',
  ),
  61 =>
  array (
    'EOL' => 'r 20',
  ),
  62 =>
  array (
    'var' => 's 63',
    'VARIABLE' => 's 33',
    'EOL' => 'r 22',
  ),
  63 =>
  array (
    'EOL' => 'r 21',
  ),
  64 =>
  array (
    'var' => 's 65',
    'VARIABLE' => 's 33',
  ),
  65 =>
  array (
    'var' => 's 66',
    'VARIABLE' => 's 33',
  ),
  66 =>
  array (
    'EOL' => 'r 23',
  ),
  67 =>
  array (
    '#' => 'r 35',
  ),
);
function reduce_0_command_1($tokens, &$result) {
#
# (0) command :=  statement
#
$result = reset($tokens);
 HfosCommandRunner::setOpCode($result);
}

function reduce_1_statement_1($tokens, &$result) {
#
# (1) statement :=  grant_statement  EOL
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_2_statement_2($tokens, &$result) {
#
# (2) statement :=  aura_statement  EOL
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_3_statement_3($tokens, &$result) {
#
# (3) statement :=  tatico_statement  EOL
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_4_statement_4($tokens, &$result) {
#
# (4) statement :=  auth_statement  EOL
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_5_statement_5($tokens, &$result) {
#
# (5) statement :=  gc_statement  EOL
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_6_statement_6($tokens, &$result) {
#
# (6) statement :=  restart_service_statement  EOL
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_7_statement_7($tokens, &$result) {
#
# (7) statement :=  backup_statement  EOL
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_8_statement_8($tokens, &$result) {
#
# (8) statement :=  show_statement  EOL
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_9_statement_9($tokens, &$result) {
#
# (9) statement :=  PS  EOL
#
$result = reset($tokens);
 $result = array('PS');
}

function reduce_10_statement_10($tokens, &$result) {
#
# (10) statement :=  WHOAMI  EOL
#
$result = reset($tokens);
 $result = array('WHOAMI');
}

function reduce_11_statement_11($tokens, &$result) {
#
# (11) statement :=  DISK  USAGE  EOL
#
$result = reset($tokens);
 $result = array('DISK_USAGE');
}

function reduce_12_statement_12($tokens, &$result) {
#
# (12) statement :=  UPTIME  EOL
#
$result = reset($tokens);
 $result = array('UPTIME');
}

function reduce_13_statement_13($tokens, &$result) {
#
# (13) statement :=  DATE  EOL
#
$result = reset($tokens);
 $result = array('DATE');
}

function reduce_14_statement_14($tokens, &$result) {
#
# (14) statement :=  HELP  EOL
#
$result = reset($tokens);
 $result = array('HELP');
}

function reduce_15_aura_statement_1($tokens, &$result) {
#
# (15) aura_statement :=  AURA  var
#
$result = reset($tokens);
$v =& $tokens[1];
 $result = array('AURA', $v);
}

function reduce_16_tatico_statement_1($tokens, &$result) {
#
# (16) tatico_statement :=  TATICO  var
#
$result = reset($tokens);
$v =& $tokens[1];
 $result = array('TATICO', $v);
}

function reduce_17_auth_statement_1($tokens, &$result) {
#
# (17) auth_statement :=  AUTH  var
#
$result = reset($tokens);
$v =& $tokens[1];
 $result = array('AUTH', $v);
}

function reduce_18_grant_statement_1($tokens, &$result) {
#
# (18) grant_statement :=  GRANT  privilege_list  ON  COMPROBS  comprob_list  TO  username
#
$result = reset($tokens);
$pl =& $tokens[1];
$cl =& $tokens[4];
$u =& $tokens[6];
 $result = array('GRANT', $pl, $cl, $u);
}

function reduce_19_gc_statement_1($tokens, &$result) {
#
# (19) gc_statement :=  GC  var
#
$result = reset($tokens);
$v =& $tokens[1];
 $result = array('GC', $v);
}

function reduce_20_restart_service_statement_1($tokens, &$result) {
#
# (20) restart_service_statement :=  RESTART  SERVICE  var
#
$result = reset($tokens);
$v =& $tokens[2];
 $result = array('RESTART_SERVICE', $v);
}

function reduce_21_backup_statement_1($tokens, &$result) {
#
# (21) backup_statement :=  BACKUP  var
#
$result = reset($tokens);
$v =& $tokens[1];
 $result = array('BACKUP', $v);
}

function reduce_22_backup_statement_2($tokens, &$result) {
#
# (22) backup_statement :=  BACKUP
#
$result = reset($tokens);
 $result = array('BACKUP');
}

function reduce_23_show_statement_1($tokens, &$result) {
#
# (23) show_statement :=  SHOW  var  var
#
$result = reset($tokens);
$v1 =& $tokens[1];
$v2 =& $tokens[2];
 $result = array('SHOW', $v1, $v2);
}

function reduce_24_privilege_list_1($tokens, &$result) {
#
# (24) privilege_list :=  privilege_list  ','  privilege
#
$result = reset($tokens);
$pl =& $tokens[0];
$p =& $tokens[2];
 $result = array('PRIV_LIST', $pl, $p);
}

function reduce_25_privilege_list_2($tokens, &$result) {
#
# (25) privilege_list :=  privilege
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_26_privilege_1($tokens, &$result) {
#
# (26) privilege :=  var
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_27_comprob_list_1($tokens, &$result) {
#
# (27) comprob_list :=  comprob_list  ','  comprob
#
$result = reset($tokens);
$cl =& $tokens[0];
$c =& $tokens[2];
 $result = array('COMPROB_LIST', $cl, $c);
}

function reduce_28_comprob_list_2($tokens, &$result) {
#
# (28) comprob_list :=  comprob
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_29_comprob_1($tokens, &$result) {
#
# (29) comprob :=  var
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_30_comprob_2($tokens, &$result) {
#
# (30) comprob :=  literal
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_31_username_1($tokens, &$result) {
#
# (31) username :=  var
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_32_username_2($tokens, &$result) {
#
# (32) username :=  literal
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_33_var_1($tokens, &$result) {
#
# (33) var :=  VARIABLE
#
$result = reset($tokens);
 $result = array('VAR', $tokens[0]);
}

function reduce_34_literal_1($tokens, &$result) {
#
# (34) literal :=  STRING
#
$result = reset($tokens);
 $result = array('LITERAL', 'STR', $tokens[0]);
}

function reduce_35_start_1($tokens, &$result) {
#
# (35) 'start' :=  command
#
$result = reset($tokens);

}

var $method = array (
  0 => 'reduce_0_command_1',
  1 => 'reduce_1_statement_1',
  2 => 'reduce_2_statement_2',
  3 => 'reduce_3_statement_3',
  4 => 'reduce_4_statement_4',
  5 => 'reduce_5_statement_5',
  6 => 'reduce_6_statement_6',
  7 => 'reduce_7_statement_7',
  8 => 'reduce_8_statement_8',
  9 => 'reduce_9_statement_9',
  10 => 'reduce_10_statement_10',
  11 => 'reduce_11_statement_11',
  12 => 'reduce_12_statement_12',
  13 => 'reduce_13_statement_13',
  14 => 'reduce_14_statement_14',
  15 => 'reduce_15_aura_statement_1',
  16 => 'reduce_16_tatico_statement_1',
  17 => 'reduce_17_auth_statement_1',
  18 => 'reduce_18_grant_statement_1',
  19 => 'reduce_19_gc_statement_1',
  20 => 'reduce_20_restart_service_statement_1',
  21 => 'reduce_21_backup_statement_1',
  22 => 'reduce_22_backup_statement_2',
  23 => 'reduce_23_show_statement_1',
  24 => 'reduce_24_privilege_list_1',
  25 => 'reduce_25_privilege_list_2',
  26 => 'reduce_26_privilege_1',
  27 => 'reduce_27_comprob_list_1',
  28 => 'reduce_28_comprob_list_2',
  29 => 'reduce_29_comprob_1',
  30 => 'reduce_30_comprob_2',
  31 => 'reduce_31_username_1',
  32 => 'reduce_32_username_2',
  33 => 'reduce_33_var_1',
  34 => 'reduce_34_literal_1',
  35 => 'reduce_35_start_1',
);
var $a = array (
  0 =>
  array (
    'symbol' => 'command',
    'len' => 1,
    'replace' => true,
  ),
  1 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  2 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  3 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  4 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  5 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  6 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  7 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  8 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  9 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  10 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  11 =>
  array (
    'symbol' => 'statement',
    'len' => 3,
    'replace' => true,
  ),
  12 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  13 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  14 =>
  array (
    'symbol' => 'statement',
    'len' => 2,
    'replace' => true,
  ),
  15 =>
  array (
    'symbol' => 'aura_statement',
    'len' => 2,
    'replace' => true,
  ),
  16 =>
  array (
    'symbol' => 'tatico_statement',
    'len' => 2,
    'replace' => true,
  ),
  17 =>
  array (
    'symbol' => 'auth_statement',
    'len' => 2,
    'replace' => true,
  ),
  18 =>
  array (
    'symbol' => 'grant_statement',
    'len' => 7,
    'replace' => true,
  ),
  19 =>
  array (
    'symbol' => 'gc_statement',
    'len' => 2,
    'replace' => true,
  ),
  20 =>
  array (
    'symbol' => 'restart_service_statement',
    'len' => 3,
    'replace' => true,
  ),
  21 =>
  array (
    'symbol' => 'backup_statement',
    'len' => 2,
    'replace' => true,
  ),
  22 =>
  array (
    'symbol' => 'backup_statement',
    'len' => 1,
    'replace' => true,
  ),
  23 =>
  array (
    'symbol' => 'show_statement',
    'len' => 3,
    'replace' => true,
  ),
  24 =>
  array (
    'symbol' => 'privilege_list',
    'len' => 3,
    'replace' => true,
  ),
  25 =>
  array (
    'symbol' => 'privilege_list',
    'len' => 1,
    'replace' => true,
  ),
  26 =>
  array (
    'symbol' => 'privilege',
    'len' => 1,
    'replace' => true,
  ),
  27 =>
  array (
    'symbol' => 'comprob_list',
    'len' => 3,
    'replace' => true,
  ),
  28 =>
  array (
    'symbol' => 'comprob_list',
    'len' => 1,
    'replace' => true,
  ),
  29 =>
  array (
    'symbol' => 'comprob',
    'len' => 1,
    'replace' => true,
  ),
  30 =>
  array (
    'symbol' => 'comprob',
    'len' => 1,
    'replace' => true,
  ),
  31 =>
  array (
    'symbol' => 'username',
    'len' => 1,
    'replace' => true,
  ),
  32 =>
  array (
    'symbol' => 'username',
    'len' => 1,
    'replace' => true,
  ),
  33 =>
  array (
    'symbol' => 'var',
    'len' => 1,
    'replace' => true,
  ),
  34 =>
  array (
    'symbol' => 'literal',
    'len' => 1,
    'replace' => true,
  ),
  35 =>
  array (
    'symbol' => '\'start\'',
    'len' => 1,
    'replace' => true,
  ),
);
}

<?php

class MacroParser extends lime_parser {
var $qi = 0;
var $i = array (
  0 => 
  array (
    'statement_list' => 's 1',
    'statement' => 's 81',
    'if_statement' => 's 3',
    'expression_statement' => 's 5',
    'print_statement' => 's 7',
    'if_statement_1' => 's 9',
    'if_statement_2' => 's 10',
    'SI' => 's 11',
    'IMPRIMIR' => 's 21',
    'expression' => 's 80',
    'exp' => 's 23',
    'equal_expr_statement' => 's 79',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
    'macro' => 's 82',
    '\'start\'' => 'a \'start\'',
  ),
  1 => 
  array (
    'statement' => 's 2',
    'if_statement' => 's 3',
    'expression_statement' => 's 5',
    'print_statement' => 's 7',
    'if_statement_1' => 's 9',
    'if_statement_2' => 's 10',
    'SI' => 's 11',
    'IMPRIMIR' => 's 21',
    'expression' => 's 80',
    'exp' => 's 23',
    'equal_expr_statement' => 's 79',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
    '#' => 'r 0',
  ),
  2 => 
  array (
    'IMPRIMIR' => 'r 1',
    'SI' => 'r 1',
    'NUMERO' => 'r 1',
    'TEXTO' => 'r 1',
    'var' => 'r 1',
    '\'(\'' => 'r 1',
    'SINO' => 'r 1',
    'FIN' => 'r 1',
    '#' => 'r 1',
  ),
  3 => 
  array (
    'EOL' => 's 4',
  ),
  4 => 
  array (
    'IMPRIMIR' => 'r 3',
    'SI' => 'r 3',
    'NUMERO' => 'r 3',
    'TEXTO' => 'r 3',
    'var' => 'r 3',
    '\'(\'' => 'r 3',
    'SINO' => 'r 3',
    'FIN' => 'r 3',
    '#' => 'r 3',
  ),
  5 => 
  array (
    'EOL' => 's 6',
  ),
  6 => 
  array (
    'IMPRIMIR' => 'r 4',
    'SI' => 'r 4',
    'NUMERO' => 'r 4',
    'TEXTO' => 'r 4',
    'var' => 'r 4',
    '\'(\'' => 'r 4',
    'SINO' => 'r 4',
    'FIN' => 'r 4',
    '#' => 'r 4',
  ),
  7 => 
  array (
    'EOL' => 's 8',
  ),
  8 => 
  array (
    'IMPRIMIR' => 'r 5',
    'SI' => 'r 5',
    'NUMERO' => 'r 5',
    'TEXTO' => 'r 5',
    'var' => 'r 5',
    '\'(\'' => 'r 5',
    'SINO' => 'r 5',
    'FIN' => 'r 5',
    '#' => 'r 5',
  ),
  9 => 
  array (
    'EOL' => 'r 6',
  ),
  10 => 
  array (
    'EOL' => 'r 7',
  ),
  11 => 
  array (
    'expression' => 's 12',
    'exp' => 's 23',
    'equal_expr_statement' => 's 79',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  12 => 
  array (
    'ENTONCES' => 's 13',
  ),
  13 => 
  array (
    'EOL' => 's 14',
  ),
  14 => 
  array (
    'statement_list' => 's 15',
    'statement' => 's 81',
    'if_statement' => 's 3',
    'expression_statement' => 's 5',
    'print_statement' => 's 7',
    'if_statement_1' => 's 9',
    'if_statement_2' => 's 10',
    'SI' => 's 11',
    'IMPRIMIR' => 's 21',
    'expression' => 's 80',
    'exp' => 's 23',
    'equal_expr_statement' => 's 79',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  15 => 
  array (
    'statement' => 's 2',
    'if_statement' => 's 3',
    'expression_statement' => 's 5',
    'print_statement' => 's 7',
    'if_statement_1' => 's 9',
    'if_statement_2' => 's 10',
    'SI' => 's 11',
    'FIN' => 's 16',
    'SINO' => 's 17',
    'IMPRIMIR' => 's 21',
    'expression' => 's 80',
    'exp' => 's 23',
    'equal_expr_statement' => 's 79',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  16 => 
  array (
    'EOL' => 'r 8',
  ),
  17 => 
  array (
    'EOL' => 's 18',
  ),
  18 => 
  array (
    'statement_list' => 's 19',
    'statement' => 's 81',
    'if_statement' => 's 3',
    'expression_statement' => 's 5',
    'print_statement' => 's 7',
    'if_statement_1' => 's 9',
    'if_statement_2' => 's 10',
    'SI' => 's 11',
    'IMPRIMIR' => 's 21',
    'expression' => 's 80',
    'exp' => 's 23',
    'equal_expr_statement' => 's 79',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  19 => 
  array (
    'statement' => 's 2',
    'if_statement' => 's 3',
    'expression_statement' => 's 5',
    'print_statement' => 's 7',
    'if_statement_1' => 's 9',
    'if_statement_2' => 's 10',
    'SI' => 's 11',
    'FIN' => 's 20',
    'IMPRIMIR' => 's 21',
    'expression' => 's 80',
    'exp' => 's 23',
    'equal_expr_statement' => 's 79',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  20 => 
  array (
    'EOL' => 'r 9',
  ),
  21 => 
  array (
    'expression' => 's 22',
    'exp' => 's 23',
    'equal_expr_statement' => 's 79',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  22 => 
  array (
    'EOL' => 'r 10',
  ),
  23 => 
  array (
    '\'=\'' => 's 24',
    'ES' => 's 75',
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 's 30',
    '\'+\'' => 's 32',
    'MENOS' => 's 34',
    '\'-\'' => 's 36',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 's 48',
    '\'<\'' => 's 53',
    'EOL' => 'r 12',
    'ENTONCES' => 'r 12',
  ),
  24 => 
  array (
    'exp' => 's 25',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  25 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 's 30',
    '\'+\'' => 's 32',
    'MENOS' => 's 34',
    '\'-\'' => 's 36',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 's 48',
    'ES' => 's 50',
    '\'<\'' => 's 53',
    'EOL' => 'r 14',
    'ENTONCES' => 'r 14',
  ),
  26 => 
  array (
    'ES' => 'r 17',
    'HORAS' => 'r 17',
    '\'<\'' => 'r 17',
    '\'>\'' => 'r 17',
    '\'/\'' => 'r 17',
    'DIVIDIDO' => 'r 17',
    '\'*\'' => 'r 17',
    'MULTIPLICADO' => 'r 17',
    'POR' => 'r 17',
    '\'-\'' => 'r 17',
    'MENOS' => 'r 17',
    '\'+\'' => 'r 17',
    'MAS' => 'r 17',
    'DIAS' => 'r 17',
    'MESES' => 'r 17',
    'ANOS' => 'r 17',
    '\'=\'' => 'r 17',
    'EOL' => 'r 17',
    'ENTONCES' => 'r 17',
    '\')\'' => 'r 17',
  ),
  27 => 
  array (
    'ES' => 'r 18',
    'HORAS' => 'r 18',
    '\'<\'' => 'r 18',
    '\'>\'' => 'r 18',
    '\'/\'' => 'r 18',
    'DIVIDIDO' => 'r 18',
    '\'*\'' => 'r 18',
    'MULTIPLICADO' => 'r 18',
    'POR' => 'r 18',
    '\'-\'' => 'r 18',
    'MENOS' => 'r 18',
    '\'+\'' => 'r 18',
    'MAS' => 'r 18',
    'DIAS' => 'r 18',
    'MESES' => 'r 18',
    'ANOS' => 'r 18',
    '\'=\'' => 'r 18',
    'EOL' => 'r 18',
    'ENTONCES' => 'r 18',
    '\')\'' => 'r 18',
  ),
  28 => 
  array (
    'ES' => 'r 19',
    'HORAS' => 'r 19',
    '\'<\'' => 'r 19',
    '\'>\'' => 'r 19',
    '\'/\'' => 'r 19',
    'DIVIDIDO' => 'r 19',
    '\'*\'' => 'r 19',
    'MULTIPLICADO' => 'r 19',
    'POR' => 'r 19',
    '\'-\'' => 'r 19',
    'MENOS' => 'r 19',
    '\'+\'' => 'r 19',
    'MAS' => 'r 19',
    'DIAS' => 'r 19',
    'MESES' => 'r 19',
    'ANOS' => 'r 19',
    '\'=\'' => 'r 19',
    'EOL' => 'r 19',
    'ENTONCES' => 'r 19',
    '\')\'' => 'r 19',
  ),
  29 => 
  array (
    'ES' => 'r 20',
    'HORAS' => 'r 20',
    '\'<\'' => 'r 20',
    '\'>\'' => 'r 20',
    '\'/\'' => 'r 20',
    'DIVIDIDO' => 'r 20',
    '\'*\'' => 'r 20',
    'MULTIPLICADO' => 'r 20',
    'POR' => 'r 20',
    '\'-\'' => 'r 20',
    'MENOS' => 'r 20',
    '\'+\'' => 'r 20',
    'MAS' => 'r 20',
    'DIAS' => 'r 20',
    'MESES' => 'r 20',
    'ANOS' => 'r 20',
    '\'=\'' => 'r 20',
    'EOL' => 'r 20',
    'ENTONCES' => 'r 20',
    '\')\'' => 'r 20',
  ),
  30 => 
  array (
    'exp' => 's 31',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  31 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 'r 27',
    '\'+\'' => 'r 27',
    'MENOS' => 'r 27',
    '\'-\'' => 'r 27',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 'r 27',
    'ES' => 'r 27',
    '\'<\'' => 'r 27',
    '\'=\'' => 'r 27',
    'EOL' => 'r 27',
    'ENTONCES' => 'r 27',
    '\')\'' => 'r 27',
  ),
  32 => 
  array (
    'exp' => 's 33',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  33 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 'r 28',
    '\'+\'' => 'r 28',
    'MENOS' => 'r 28',
    '\'-\'' => 'r 28',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 'r 28',
    'ES' => 'r 28',
    '\'<\'' => 'r 28',
    '\'=\'' => 'r 28',
    'EOL' => 'r 28',
    'ENTONCES' => 'r 28',
    '\')\'' => 'r 28',
  ),
  34 => 
  array (
    'exp' => 's 35',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  35 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 'r 29',
    '\'+\'' => 'r 29',
    'MENOS' => 'r 29',
    '\'-\'' => 'r 29',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 'r 29',
    'ES' => 'r 29',
    '\'<\'' => 'r 29',
    '\'=\'' => 'r 29',
    'EOL' => 'r 29',
    'ENTONCES' => 'r 29',
    '\')\'' => 'r 29',
  ),
  36 => 
  array (
    'exp' => 's 37',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  37 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 'r 30',
    '\'+\'' => 'r 30',
    'MENOS' => 'r 30',
    '\'-\'' => 'r 30',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 'r 30',
    'ES' => 'r 30',
    '\'<\'' => 'r 30',
    '\'=\'' => 'r 30',
    'EOL' => 'r 30',
    'ENTONCES' => 'r 30',
    '\')\'' => 'r 30',
  ),
  38 => 
  array (
    'exp' => 's 39',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  39 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 'r 31',
    '\'+\'' => 'r 31',
    'MENOS' => 'r 31',
    '\'-\'' => 'r 31',
    'POR' => 'r 31',
    'MULTIPLICADO' => 'r 31',
    '\'*\'' => 'r 31',
    'DIVIDIDO' => 'r 31',
    '\'/\'' => 'r 31',
    '\'>\'' => 'r 31',
    'ES' => 'r 31',
    '\'<\'' => 'r 31',
    '\'=\'' => 'r 31',
    'EOL' => 'r 31',
    'ENTONCES' => 'r 31',
    '\')\'' => 'r 31',
  ),
  40 => 
  array (
    'exp' => 's 41',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'POR' => 's 73',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  41 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 'r 32',
    '\'+\'' => 'r 32',
    'MENOS' => 'r 32',
    '\'-\'' => 'r 32',
    'POR' => 'r 32',
    'MULTIPLICADO' => 'r 32',
    '\'*\'' => 'r 32',
    'DIVIDIDO' => 'r 32',
    '\'/\'' => 'r 32',
    '\'>\'' => 'r 32',
    'ES' => 'r 32',
    '\'<\'' => 'r 32',
    '\'=\'' => 'r 32',
    'EOL' => 'r 32',
    'ENTONCES' => 'r 32',
    '\')\'' => 'r 32',
  ),
  42 => 
  array (
    'exp' => 's 43',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  43 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 'r 34',
    '\'+\'' => 'r 34',
    'MENOS' => 'r 34',
    '\'-\'' => 'r 34',
    'POR' => 'r 34',
    'MULTIPLICADO' => 'r 34',
    '\'*\'' => 'r 34',
    'DIVIDIDO' => 'r 34',
    '\'/\'' => 'r 34',
    '\'>\'' => 'r 34',
    'ES' => 'r 34',
    '\'<\'' => 'r 34',
    '\'=\'' => 'r 34',
    'EOL' => 'r 34',
    'ENTONCES' => 'r 34',
    '\')\'' => 'r 34',
  ),
  44 => 
  array (
    'exp' => 's 45',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  45 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 'r 35',
    '\'+\'' => 'r 35',
    'MENOS' => 'r 35',
    '\'-\'' => 'r 35',
    'POR' => 'r 35',
    'MULTIPLICADO' => 'r 35',
    '\'*\'' => 'r 35',
    'DIVIDIDO' => 'r 35',
    '\'/\'' => 'r 35',
    '\'>\'' => 'r 35',
    'ES' => 'r 35',
    '\'<\'' => 'r 35',
    '\'=\'' => 'r 35',
    'EOL' => 'r 35',
    'ENTONCES' => 'r 35',
    '\')\'' => 'r 35',
  ),
  46 => 
  array (
    'exp' => 's 47',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  47 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 'r 36',
    '\'+\'' => 'r 36',
    'MENOS' => 'r 36',
    '\'-\'' => 'r 36',
    'POR' => 'r 36',
    'MULTIPLICADO' => 'r 36',
    '\'*\'' => 'r 36',
    'DIVIDIDO' => 'r 36',
    '\'/\'' => 'r 36',
    '\'>\'' => 'r 36',
    'ES' => 'r 36',
    '\'<\'' => 'r 36',
    '\'=\'' => 'r 36',
    'EOL' => 'r 36',
    'ENTONCES' => 'r 36',
    '\')\'' => 'r 36',
  ),
  48 => 
  array (
    'exp' => 's 49',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  49 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 's 30',
    '\'+\'' => 's 32',
    'MENOS' => 's 34',
    '\'-\'' => 's 36',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 'r 37',
    'ES' => 'r 37',
    '\'<\'' => 'r 37',
    '\'=\'' => 'r 37',
    'EOL' => 'r 37',
    'ENTONCES' => 'r 37',
    '\')\'' => 'r 37',
  ),
  50 => 
  array (
    'MAYOR' => 's 51',
    'MENOR' => 's 71',
  ),
  51 => 
  array (
    'exp' => 's 52',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  52 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 's 30',
    '\'+\'' => 's 32',
    'MENOS' => 's 34',
    '\'-\'' => 's 36',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 's 48',
    'ES' => 's 50',
    '\'<\'' => 's 53',
    'EOL' => 'r 38',
    'ENTONCES' => 'r 38',
    '\'=\'' => 'r 38',
    '\')\'' => 'r 38',
  ),
  53 => 
  array (
    'exp' => 's 54',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  54 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 's 30',
    '\'+\'' => 's 32',
    'MENOS' => 's 34',
    '\'-\'' => 's 36',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 'r 39',
    'ES' => 'r 39',
    '\'<\'' => 'r 39',
    '\'=\'' => 'r 39',
    'EOL' => 'r 39',
    'ENTONCES' => 'r 39',
    '\')\'' => 'r 39',
  ),
  55 => 
  array (
    '\'.\'' => 's 56',
    'DEL' => 's 58',
    'DE' => 's 60',
    'HORAS' => 'r 24',
    'ES' => 'r 24',
    '\'<\'' => 'r 24',
    '\'>\'' => 'r 24',
    '\'/\'' => 'r 24',
    'DIVIDIDO' => 'r 24',
    '\'*\'' => 'r 24',
    'MULTIPLICADO' => 'r 24',
    'POR' => 'r 24',
    '\'-\'' => 'r 24',
    'MENOS' => 'r 24',
    '\'+\'' => 'r 24',
    'MAS' => 'r 24',
    'DIAS' => 'r 24',
    'MESES' => 'r 24',
    'ANOS' => 'r 24',
    '\'=\'' => 'r 24',
    'EOL' => 'r 24',
    'ENTONCES' => 'r 24',
    '\')\'' => 'r 24',
  ),
  56 => 
  array (
    'var' => 's 57',
  ),
  57 => 
  array (
    'HORAS' => 'r 21',
    'ES' => 'r 21',
    '\'<\'' => 'r 21',
    '\'>\'' => 'r 21',
    '\'/\'' => 'r 21',
    'DIVIDIDO' => 'r 21',
    '\'*\'' => 'r 21',
    'MULTIPLICADO' => 'r 21',
    'POR' => 'r 21',
    '\'-\'' => 'r 21',
    'MENOS' => 'r 21',
    '\'+\'' => 'r 21',
    'MAS' => 'r 21',
    'DIAS' => 'r 21',
    'MESES' => 'r 21',
    'ANOS' => 'r 21',
    '\'=\'' => 'r 21',
    'EOL' => 'r 21',
    'ENTONCES' => 'r 21',
    '\')\'' => 'r 21',
  ),
  58 => 
  array (
    'var' => 's 59',
  ),
  59 => 
  array (
    'HORAS' => 'r 22',
    'ES' => 'r 22',
    '\'<\'' => 'r 22',
    '\'>\'' => 'r 22',
    '\'/\'' => 'r 22',
    'DIVIDIDO' => 'r 22',
    '\'*\'' => 'r 22',
    'MULTIPLICADO' => 'r 22',
    'POR' => 'r 22',
    '\'-\'' => 'r 22',
    'MENOS' => 'r 22',
    '\'+\'' => 'r 22',
    'MAS' => 'r 22',
    'DIAS' => 'r 22',
    'MESES' => 'r 22',
    'ANOS' => 'r 22',
    '\'=\'' => 'r 22',
    'EOL' => 'r 22',
    'ENTONCES' => 'r 22',
    '\')\'' => 'r 22',
  ),
  60 => 
  array (
    'var' => 's 61',
  ),
  61 => 
  array (
    'HORAS' => 'r 23',
    'ES' => 'r 23',
    '\'<\'' => 'r 23',
    '\'>\'' => 'r 23',
    '\'/\'' => 'r 23',
    'DIVIDIDO' => 'r 23',
    '\'*\'' => 'r 23',
    'MULTIPLICADO' => 'r 23',
    'POR' => 'r 23',
    '\'-\'' => 'r 23',
    'MENOS' => 'r 23',
    '\'+\'' => 'r 23',
    'MAS' => 'r 23',
    'DIAS' => 'r 23',
    'MESES' => 'r 23',
    'ANOS' => 'r 23',
    '\'=\'' => 'r 23',
    'EOL' => 'r 23',
    'ENTONCES' => 'r 23',
    '\')\'' => 'r 23',
  ),
  62 => 
  array (
    '\'%\'' => 's 63',
    'HORAS' => 'r 25',
    'ES' => 'r 25',
    '\'<\'' => 'r 25',
    '\'>\'' => 'r 25',
    '\'/\'' => 'r 25',
    'DIVIDIDO' => 'r 25',
    '\'*\'' => 'r 25',
    'MULTIPLICADO' => 'r 25',
    'POR' => 'r 25',
    '\'-\'' => 'r 25',
    'MENOS' => 'r 25',
    '\'+\'' => 'r 25',
    'MAS' => 'r 25',
    'DIAS' => 'r 25',
    'MESES' => 'r 25',
    'ANOS' => 'r 25',
    '\'=\'' => 'r 25',
    'EOL' => 'r 25',
    'ENTONCES' => 'r 25',
    '\')\'' => 'r 25',
  ),
  63 => 
  array (
    'HORAS' => 'r 41',
    'ES' => 'r 41',
    '\'<\'' => 'r 41',
    '\'>\'' => 'r 41',
    '\'/\'' => 'r 41',
    'DIVIDIDO' => 'r 41',
    '\'*\'' => 'r 41',
    'MULTIPLICADO' => 'r 41',
    'POR' => 'r 41',
    '\'-\'' => 'r 41',
    'MENOS' => 'r 41',
    '\'+\'' => 'r 41',
    'MAS' => 'r 41',
    'DIAS' => 'r 41',
    'MESES' => 'r 41',
    'ANOS' => 'r 41',
    '\'=\'' => 'r 41',
    'EOL' => 'r 41',
    'ENTONCES' => 'r 41',
    '\')\'' => 'r 41',
  ),
  64 => 
  array (
    'HORAS' => 'r 26',
    'ES' => 'r 26',
    '\'<\'' => 'r 26',
    '\'>\'' => 'r 26',
    '\'/\'' => 'r 26',
    'DIVIDIDO' => 'r 26',
    '\'*\'' => 'r 26',
    'MULTIPLICADO' => 'r 26',
    'POR' => 'r 26',
    '\'-\'' => 'r 26',
    'MENOS' => 'r 26',
    '\'+\'' => 'r 26',
    'MAS' => 'r 26',
    'DIAS' => 'r 26',
    'MESES' => 'r 26',
    'ANOS' => 'r 26',
    '\'=\'' => 'r 26',
    'EOL' => 'r 26',
    'ENTONCES' => 'r 26',
    '\')\'' => 'r 26',
  ),
  65 => 
  array (
    'HORAS' => 'r 42',
    'ES' => 'r 42',
    '\'<\'' => 'r 42',
    '\'>\'' => 'r 42',
    '\'/\'' => 'r 42',
    'DIVIDIDO' => 'r 42',
    '\'*\'' => 'r 42',
    'MULTIPLICADO' => 'r 42',
    'POR' => 'r 42',
    '\'-\'' => 'r 42',
    'MENOS' => 'r 42',
    '\'+\'' => 'r 42',
    'MAS' => 'r 42',
    'DIAS' => 'r 42',
    'MESES' => 'r 42',
    'ANOS' => 'r 42',
    '\'=\'' => 'r 42',
    'EOL' => 'r 42',
    'ENTONCES' => 'r 42',
    '\')\'' => 'r 42',
  ),
  66 => 
  array (
    'HORAS' => 'r 43',
    'ES' => 'r 43',
    '\'<\'' => 'r 43',
    '\'>\'' => 'r 43',
    '\'/\'' => 'r 43',
    'DIVIDIDO' => 'r 43',
    '\'*\'' => 'r 43',
    'MULTIPLICADO' => 'r 43',
    'POR' => 'r 43',
    '\'-\'' => 'r 43',
    'MENOS' => 'r 43',
    '\'+\'' => 'r 43',
    'MAS' => 'r 43',
    'DIAS' => 'r 43',
    'MESES' => 'r 43',
    'ANOS' => 'r 43',
    '\'=\'' => 'r 43',
    'EOL' => 'r 43',
    'ENTONCES' => 'r 43',
    '\')\'' => 'r 43',
  ),
  67 => 
  array (
    'ES' => 'r 44',
    'HORAS' => 'r 44',
    '\'<\'' => 'r 44',
    '\'>\'' => 'r 44',
    '\'/\'' => 'r 44',
    'DIVIDIDO' => 'r 44',
    '\'*\'' => 'r 44',
    'MULTIPLICADO' => 'r 44',
    'POR' => 'r 44',
    '\'-\'' => 'r 44',
    'MENOS' => 'r 44',
    '\'+\'' => 'r 44',
    'MAS' => 'r 44',
    'DIAS' => 'r 44',
    'MESES' => 'r 44',
    'ANOS' => 'r 44',
    '\'=\'' => 'r 44',
    'EOL' => 'r 44',
    'ENTONCES' => 'r 44',
    '\')\'' => 'r 44',
  ),
  68 => 
  array (
    'exp' => 's 69',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  69 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 's 30',
    '\'+\'' => 's 32',
    'MENOS' => 's 34',
    '\'-\'' => 's 36',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 's 48',
    'ES' => 's 50',
    '\'<\'' => 's 53',
    '\')\'' => 's 70',
  ),
  70 => 
  array (
    'ES' => 'r 45',
    'HORAS' => 'r 45',
    '\'<\'' => 'r 45',
    '\'>\'' => 'r 45',
    '\'/\'' => 'r 45',
    'DIVIDIDO' => 'r 45',
    '\'*\'' => 'r 45',
    'MULTIPLICADO' => 'r 45',
    'POR' => 'r 45',
    '\'-\'' => 'r 45',
    'MENOS' => 'r 45',
    '\'+\'' => 'r 45',
    'MAS' => 'r 45',
    'DIAS' => 'r 45',
    'MESES' => 'r 45',
    'ANOS' => 'r 45',
    '\'=\'' => 'r 45',
    'EOL' => 'r 45',
    'ENTONCES' => 'r 45',
    '\')\'' => 'r 45',
  ),
  71 => 
  array (
    'exp' => 's 72',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  72 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 's 30',
    '\'+\'' => 's 32',
    'MENOS' => 's 34',
    '\'-\'' => 's 36',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 's 48',
    'ES' => 's 50',
    '\'<\'' => 's 53',
    'EOL' => 'r 40',
    'ENTONCES' => 'r 40',
    '\'=\'' => 'r 40',
    '\')\'' => 'r 40',
  ),
  73 => 
  array (
    'exp' => 's 74',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  74 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 'r 33',
    '\'+\'' => 'r 33',
    'MENOS' => 'r 33',
    '\'-\'' => 'r 33',
    'POR' => 'r 33',
    'MULTIPLICADO' => 'r 33',
    '\'*\'' => 'r 33',
    'DIVIDIDO' => 'r 33',
    '\'/\'' => 'r 33',
    '\'>\'' => 'r 33',
    'ES' => 'r 33',
    '\'<\'' => 'r 33',
    '\'=\'' => 'r 33',
    'EOL' => 'r 33',
    'ENTONCES' => 'r 33',
    '\')\'' => 'r 33',
  ),
  75 => 
  array (
    'IGUAL' => 's 76',
    'exp' => 's 78',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'MAYOR' => 's 51',
    'MENOR' => 's 71',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  76 => 
  array (
    'exp' => 's 77',
    'var' => 's 55',
    'NUMERO' => 's 62',
    'TEXTO' => 's 64',
    'literal' => 's 65',
    'variable' => 's 66',
    'interval' => 's 67',
    '\'(\'' => 's 68',
  ),
  77 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 's 30',
    '\'+\'' => 's 32',
    'MENOS' => 's 34',
    '\'-\'' => 's 36',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 's 48',
    'ES' => 's 50',
    '\'<\'' => 's 53',
    'EOL' => 'r 15',
    'ENTONCES' => 'r 15',
  ),
  78 => 
  array (
    'ANOS' => 's 26',
    'MESES' => 's 27',
    'DIAS' => 's 28',
    'HORAS' => 's 29',
    'MAS' => 's 30',
    '\'+\'' => 's 32',
    'MENOS' => 's 34',
    '\'-\'' => 's 36',
    'POR' => 's 38',
    'MULTIPLICADO' => 's 40',
    '\'*\'' => 's 42',
    'DIVIDIDO' => 's 44',
    '\'/\'' => 's 46',
    '\'>\'' => 's 48',
    'ES' => 's 50',
    '\'<\'' => 's 53',
    'EOL' => 'r 16',
    'ENTONCES' => 'r 16',
  ),
  79 => 
  array (
    'EOL' => 'r 13',
    'ENTONCES' => 'r 13',
  ),
  80 => 
  array (
    'EOL' => 'r 11',
  ),
  81 => 
  array (
    'IMPRIMIR' => 'r 2',
    'SI' => 'r 2',
    'NUMERO' => 'r 2',
    'TEXTO' => 'r 2',
    'var' => 'r 2',
    '\'(\'' => 'r 2',
    'SINO' => 'r 2',
    'FIN' => 'r 2',
    '#' => 'r 2',
  ),
  82 => 
  array (
    '#' => 'r 46',
  ),
);
function reduce_0_macro_1($tokens, &$result) {
#
# (0) macro :=  statement_list
#
$result = reset($tokens);
 ClaraMacro::setMacroCode($result); 
}

function reduce_1_statement_list_1($tokens, &$result) {
#
# (1) statement_list :=  statement_list  statement
#
$result = reset($tokens);
$st =& $tokens[0];
$s =& $tokens[1];
 $result = array('STMT_LIST', $st, $s); 
}

function reduce_2_statement_list_2($tokens, &$result) {
#
# (2) statement_list :=  statement
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_3_statement_1($tokens, &$result) {
#
# (3) statement :=  if_statement  EOL
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_4_statement_2($tokens, &$result) {
#
# (4) statement :=  expression_statement  EOL
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_5_statement_3($tokens, &$result) {
#
# (5) statement :=  print_statement  EOL
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_6_if_statement_1($tokens, &$result) {
#
# (6) if_statement :=  if_statement_1
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_7_if_statement_2($tokens, &$result) {
#
# (7) if_statement :=  if_statement_2
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_8_if_statement_1_1($tokens, &$result) {
#
# (8) if_statement_1 :=  SI  expression  ENTONCES  EOL  statement_list  FIN
#
$result = reset($tokens);
$e =& $tokens[1];
$st =& $tokens[4];
 $result = array('IF', $e, $st); 
}

function reduce_9_if_statement_2_1($tokens, &$result) {
#
# (9) if_statement_2 :=  SI  expression  ENTONCES  EOL  statement_list  SINO  EOL  statement_list  FIN
#
$result = reset($tokens);
$e =& $tokens[1];
$st =& $tokens[4];
$ste =& $tokens[7];
 $result = array('IF', $e, $st, $ste); 
}

function reduce_10_print_statement_1($tokens, &$result) {
#
# (10) print_statement :=  IMPRIMIR  expression
#
$result = reset($tokens);
 $result = array('PRINT', $tokens[1]); 
}

function reduce_11_expression_statement_1($tokens, &$result) {
#
# (11) expression_statement :=  expression
#
$result = reset($tokens);
 $result = array('EXPR', $tokens[0]); 
}

function reduce_12_expression_1($tokens, &$result) {
#
# (12) expression :=  exp
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_13_expression_2($tokens, &$result) {
#
# (13) expression :=  equal_expr_statement
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_14_equal_expr_statement_1($tokens, &$result) {
#
# (14) equal_expr_statement :=  exp  '='  exp
#
$result = reset($tokens);
$e =& $tokens[0];
$e =& $tokens[2];
 $result = array('EQUAL', $tokens[0], $tokens[2]); 
}

function reduce_15_equal_expr_statement_2($tokens, &$result) {
#
# (15) equal_expr_statement :=  exp  ES  IGUAL  exp
#
$result = reset($tokens);
$e =& $tokens[0];
$e =& $tokens[3];
 $result = array('EQUAL', $tokens[0], $tokens[3]); 
}

function reduce_16_equal_expr_statement_3($tokens, &$result) {
#
# (16) equal_expr_statement :=  exp  ES  exp
#
$result = reset($tokens);
$e =& $tokens[0];
$e =& $tokens[2];
 $result = array('EQUAL', $tokens[0], $tokens[2]); 
}

function reduce_17_interval_1($tokens, &$result) {
#
# (17) interval :=  exp  ANOS
#
$result = reset($tokens);
 $result = array('INTERVAL', $tokens[0], 'ANOS'); 
}

function reduce_18_interval_2($tokens, &$result) {
#
# (18) interval :=  exp  MESES
#
$result = reset($tokens);
 $result = array('INTERVAL', $tokens[0], 'MESES'); 
}

function reduce_19_interval_3($tokens, &$result) {
#
# (19) interval :=  exp  DIAS
#
$result = reset($tokens);
 $result = array('INTERVAL', $tokens[0], 'DIAS'); 
}

function reduce_20_interval_4($tokens, &$result) {
#
# (20) interval :=  exp  HORAS
#
$result = reset($tokens);
 $result = array('INTERVAL', $tokens[0], 'HORAS'); 
}

function reduce_21_variable_1($tokens, &$result) {
#
# (21) variable :=  var  '.'  var
#
$result = reset($tokens);

	$result = array('VAR', $tokens[0], $tokens[2]);
	ClaraMacro::addStaticSymbol($tokens[0], $tokens[2]);

}

function reduce_22_variable_2($tokens, &$result) {
#
# (22) variable :=  var  DEL  var
#
$result = reset($tokens);

	$result = array('VAR', $tokens[2], $tokens[0]);
	ClaraMacro::addStaticSymbol($tokens[2], $tokens[0]);

}

function reduce_23_variable_3($tokens, &$result) {
#
# (23) variable :=  var  DE  var
#
$result = reset($tokens);

	$result = array('VAR', $tokens[2], $tokens[0]);
	ClaraMacro::addStaticSymbol($tokens[2], $tokens[0]);

}

function reduce_24_variable_4($tokens, &$result) {
#
# (24) variable :=  var
#
$result = reset($tokens);

	$result = array('VAR', $tokens[0]);
	ClaraMacro::addStaticSymbol($tokens[0]);

}

function reduce_25_literal_1($tokens, &$result) {
#
# (25) literal :=  NUMERO
#
$result = reset($tokens);
 $result = array('LITERAL', 'NUM', $tokens[0]); 
}

function reduce_26_literal_2($tokens, &$result) {
#
# (26) literal :=  TEXTO
#
$result = reset($tokens);
 $result = array('LITERAL', 'STR', $tokens[0]); 
}

function reduce_27_exp_1($tokens, &$result) {
#
# (27) exp :=  exp  MAS  exp
#
$result = reset($tokens);
 $result = array('OP', '+', $tokens[0], $tokens[2]); 
}

function reduce_28_exp_2($tokens, &$result) {
#
# (28) exp :=  exp  '+'  exp
#
$result = reset($tokens);
 $result = array('OP', '+', $tokens[0], $tokens[2]); 
}

function reduce_29_exp_3($tokens, &$result) {
#
# (29) exp :=  exp  MENOS  exp
#
$result = reset($tokens);
 $result = array('OP', '-', $tokens[0], $tokens[2]); 
}

function reduce_30_exp_4($tokens, &$result) {
#
# (30) exp :=  exp  '-'  exp
#
$result = reset($tokens);
 $result = array('OP', '-', $tokens[0], $tokens[2]); 
}

function reduce_31_exp_5($tokens, &$result) {
#
# (31) exp :=  exp  POR  exp
#
$result = reset($tokens);
 $result = array('OP', '*', $tokens[0], $tokens[2]); 
}

function reduce_32_exp_6($tokens, &$result) {
#
# (32) exp :=  exp  MULTIPLICADO  exp
#
$result = reset($tokens);
 $result = array('OP', '*', $tokens[0], $tokens[2]); 
}

function reduce_33_exp_7($tokens, &$result) {
#
# (33) exp :=  exp  MULTIPLICADO  POR  exp
#
$result = reset($tokens);
 $result = array('OP', '*', $tokens[0], $tokens[3]); 
}

function reduce_34_exp_8($tokens, &$result) {
#
# (34) exp :=  exp  '*'  exp
#
$result = reset($tokens);
 $result = array('OP', '*', $tokens[0], $tokens[2]); 
}

function reduce_35_exp_9($tokens, &$result) {
#
# (35) exp :=  exp  DIVIDIDO  exp
#
$result = reset($tokens);
 $result = array('OP', '/', $tokens[0], $tokens[2]); 
}

function reduce_36_exp_10($tokens, &$result) {
#
# (36) exp :=  exp  '/'  exp
#
$result = reset($tokens);
 $result = array('OP', '/', $tokens[0], $tokens[2]); 
}

function reduce_37_exp_11($tokens, &$result) {
#
# (37) exp :=  exp  '>'  exp
#
$result = reset($tokens);
 $result = array('OP', '>', $tokens[0], $tokens[2]); 
}

function reduce_38_exp_12($tokens, &$result) {
#
# (38) exp :=  exp  ES  MAYOR  exp
#
$result = reset($tokens);
 $result = array('OP', '>', $tokens[0], $tokens[3]); 
}

function reduce_39_exp_13($tokens, &$result) {
#
# (39) exp :=  exp  '<'  exp
#
$result = reset($tokens);
 $result = array('OP', '<', $tokens[0], $tokens[2]); 
}

function reduce_40_exp_14($tokens, &$result) {
#
# (40) exp :=  exp  ES  MENOR  exp
#
$result = reset($tokens);
 $result = array('OP', '<', $tokens[0], $tokens[3]); 
}

function reduce_41_exp_15($tokens, &$result) {
#
# (41) exp :=  NUMERO  '%'
#
$result = reset($tokens);
 $result = array('PERCENT', $tokens[0]); 
}

function reduce_42_exp_16($tokens, &$result) {
#
# (42) exp :=  literal
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_43_exp_17($tokens, &$result) {
#
# (43) exp :=  variable
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_44_exp_18($tokens, &$result) {
#
# (44) exp :=  interval
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_45_exp_19($tokens, &$result) {
#
# (45) exp :=  '('  exp  ')'
#
$result = $tokens[1];

}

function reduce_46_start_1($tokens, &$result) {
#
# (46) 'start' :=  macro
#
$result = reset($tokens);

}

var $method = array (
  0 => 'reduce_0_macro_1',
  1 => 'reduce_1_statement_list_1',
  2 => 'reduce_2_statement_list_2',
  3 => 'reduce_3_statement_1',
  4 => 'reduce_4_statement_2',
  5 => 'reduce_5_statement_3',
  6 => 'reduce_6_if_statement_1',
  7 => 'reduce_7_if_statement_2',
  8 => 'reduce_8_if_statement_1_1',
  9 => 'reduce_9_if_statement_2_1',
  10 => 'reduce_10_print_statement_1',
  11 => 'reduce_11_expression_statement_1',
  12 => 'reduce_12_expression_1',
  13 => 'reduce_13_expression_2',
  14 => 'reduce_14_equal_expr_statement_1',
  15 => 'reduce_15_equal_expr_statement_2',
  16 => 'reduce_16_equal_expr_statement_3',
  17 => 'reduce_17_interval_1',
  18 => 'reduce_18_interval_2',
  19 => 'reduce_19_interval_3',
  20 => 'reduce_20_interval_4',
  21 => 'reduce_21_variable_1',
  22 => 'reduce_22_variable_2',
  23 => 'reduce_23_variable_3',
  24 => 'reduce_24_variable_4',
  25 => 'reduce_25_literal_1',
  26 => 'reduce_26_literal_2',
  27 => 'reduce_27_exp_1',
  28 => 'reduce_28_exp_2',
  29 => 'reduce_29_exp_3',
  30 => 'reduce_30_exp_4',
  31 => 'reduce_31_exp_5',
  32 => 'reduce_32_exp_6',
  33 => 'reduce_33_exp_7',
  34 => 'reduce_34_exp_8',
  35 => 'reduce_35_exp_9',
  36 => 'reduce_36_exp_10',
  37 => 'reduce_37_exp_11',
  38 => 'reduce_38_exp_12',
  39 => 'reduce_39_exp_13',
  40 => 'reduce_40_exp_14',
  41 => 'reduce_41_exp_15',
  42 => 'reduce_42_exp_16',
  43 => 'reduce_43_exp_17',
  44 => 'reduce_44_exp_18',
  45 => 'reduce_45_exp_19',
  46 => 'reduce_46_start_1',
);
var $a = array (
  0 => 
  array (
    'symbol' => 'macro',
    'len' => 1,
    'replace' => true,
  ),
  1 => 
  array (
    'symbol' => 'statement_list',
    'len' => 2,
    'replace' => true,
  ),
  2 => 
  array (
    'symbol' => 'statement_list',
    'len' => 1,
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
    'symbol' => 'if_statement',
    'len' => 1,
    'replace' => true,
  ),
  7 => 
  array (
    'symbol' => 'if_statement',
    'len' => 1,
    'replace' => true,
  ),
  8 => 
  array (
    'symbol' => 'if_statement_1',
    'len' => 6,
    'replace' => true,
  ),
  9 => 
  array (
    'symbol' => 'if_statement_2',
    'len' => 9,
    'replace' => true,
  ),
  10 => 
  array (
    'symbol' => 'print_statement',
    'len' => 2,
    'replace' => true,
  ),
  11 => 
  array (
    'symbol' => 'expression_statement',
    'len' => 1,
    'replace' => true,
  ),
  12 => 
  array (
    'symbol' => 'expression',
    'len' => 1,
    'replace' => true,
  ),
  13 => 
  array (
    'symbol' => 'expression',
    'len' => 1,
    'replace' => true,
  ),
  14 => 
  array (
    'symbol' => 'equal_expr_statement',
    'len' => 3,
    'replace' => true,
  ),
  15 => 
  array (
    'symbol' => 'equal_expr_statement',
    'len' => 4,
    'replace' => true,
  ),
  16 => 
  array (
    'symbol' => 'equal_expr_statement',
    'len' => 3,
    'replace' => true,
  ),
  17 => 
  array (
    'symbol' => 'interval',
    'len' => 2,
    'replace' => true,
  ),
  18 => 
  array (
    'symbol' => 'interval',
    'len' => 2,
    'replace' => true,
  ),
  19 => 
  array (
    'symbol' => 'interval',
    'len' => 2,
    'replace' => true,
  ),
  20 => 
  array (
    'symbol' => 'interval',
    'len' => 2,
    'replace' => true,
  ),
  21 => 
  array (
    'symbol' => 'variable',
    'len' => 3,
    'replace' => true,
  ),
  22 => 
  array (
    'symbol' => 'variable',
    'len' => 3,
    'replace' => true,
  ),
  23 => 
  array (
    'symbol' => 'variable',
    'len' => 3,
    'replace' => true,
  ),
  24 => 
  array (
    'symbol' => 'variable',
    'len' => 1,
    'replace' => true,
  ),
  25 => 
  array (
    'symbol' => 'literal',
    'len' => 1,
    'replace' => true,
  ),
  26 => 
  array (
    'symbol' => 'literal',
    'len' => 1,
    'replace' => true,
  ),
  27 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  28 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  29 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  30 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  31 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  32 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  33 => 
  array (
    'symbol' => 'exp',
    'len' => 4,
    'replace' => true,
  ),
  34 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  35 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  36 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  37 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  38 => 
  array (
    'symbol' => 'exp',
    'len' => 4,
    'replace' => true,
  ),
  39 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  40 => 
  array (
    'symbol' => 'exp',
    'len' => 4,
    'replace' => true,
  ),
  41 => 
  array (
    'symbol' => 'exp',
    'len' => 2,
    'replace' => true,
  ),
  42 => 
  array (
    'symbol' => 'exp',
    'len' => 1,
    'replace' => true,
  ),
  43 => 
  array (
    'symbol' => 'exp',
    'len' => 1,
    'replace' => true,
  ),
  44 => 
  array (
    'symbol' => 'exp',
    'len' => 1,
    'replace' => true,
  ),
  45 => 
  array (
    'symbol' => 'exp',
    'len' => 3,
    'replace' => true,
  ),
  46 => 
  array (
    'symbol' => '\'start\'',
    'len' => 1,
    'replace' => true,
  ),
);
}

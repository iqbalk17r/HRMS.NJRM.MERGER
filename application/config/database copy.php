<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;


/* Koneksi ke MSSQL 2000 ci_v2
$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'sa';
$db['default']['password'] = '';
$db['default']['database'] = 'd_master';
$db['default']['dbdriver'] = 'mssql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

ci_versi 3
$db['default'] = array(
    'dsn'	=> '',
    'hostname' => 'localhost',
    'username' => 'postgres',
    'password' => 'root',
    'database' => 'SIS1',
    'dbdriver' => 'postgre',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

*/
/* Koneksi ke MS Acces BJM */
$db['DBBJM']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM.mdb';
//$db['DBBJM']['hostname'] = 'sby_lokal';
$db['DBBJM']['username'] = '';
$db['DBBJM']['password'] = '';
$db['DBBJM']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM.mdb';
//$db['DBBJM']['database'] = 'sby_lokal';
$db['DBBJM']['dbdriver'] = 'odbc';
$db['DBBJM']['dbprefix'] = '';
$db['DBBJM']['pconnect'] = TRUE;
$db['DBBJM']['db_debug'] = TRUE;
$db['DBBJM']['cache_on'] = FALSE;
$db['DBBJM']['cachedir'] = '';
$db['DBBJM']['char_set'] = 'utf8';
$db['DBBJM']['dbcollat'] = 'utf8_general_ci';
$db['DBBJM']['swap_pre'] = '';
$db['DBBJM']['autoinit'] = TRUE;
$db['DBBJM']['stricton'] = FALSE;

/* Koneksi ke MS Acces SMD*/
$db['DBSMD']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM02.mdb';
$db['DBSMD']['username'] = '';
$db['DBSMD']['password'] = '';
$db['DBSMD']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM02.mdb';
$db['DBSMD']['dbdriver'] = 'odbc';
$db['DBSMD']['dbprefix'] = '';
$db['DBSMD']['pconnect'] = TRUE;
$db['DBSMD']['db_debug'] = TRUE;
$db['DBSMD']['cache_on'] = FALSE;
$db['DBSMD']['cachedir'] = '';
$db['DBSMD']['char_set'] = 'utf8';
$db['DBSMD']['dbcollat'] = 'utf8_general_ci';
$db['DBSMD']['swap_pre'] = '';
$db['DBSMD']['autoinit'] = TRUE;
$db['DBSMD']['stricton'] = FALSE;

/* Koneksi ke MS Acces BPP */
$db['DBBPP']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM03.mdb';
$db['DBBPP']['username'] = '';
$db['DBBPP']['password'] = '';
$db['DBBPP']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM03.mdb';
$db['DBBPP']['dbdriver'] = 'odbc';
$db['DBBPP']['dbprefix'] = '';
$db['DBBPP']['pconnect'] = TRUE;
$db['DBBPP']['db_debug'] = TRUE;
$db['DBBPP']['cache_on'] = FALSE;
$db['DBBPP']['cachedir'] = '';
$db['DBBPP']['char_set'] = 'utf8';
$db['DBBPP']['dbcollat'] = 'utf8_general_ci';
$db['DBBPP']['swap_pre'] = '';
$db['DBBPP']['autoinit'] = TRUE;
$db['DBBPP']['stricton'] = FALSE;

/* Koneksi ke MS Acces PKA */
$db['DBPKA']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM04.mdb';
$db['DBPKA']['username'] = '';
$db['DBPKA']['password'] = '';
$db['DBPKA']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM04.mdb';
$db['DBPKA']['dbdriver'] = 'odbc';
$db['DBPKA']['dbprefix'] = '';
$db['DBPKA']['pconnect'] = TRUE;
$db['DBPKA']['db_debug'] = TRUE;
$db['DBPKA']['cache_on'] = FALSE;
$db['DBPKA']['cachedir'] = '';
$db['DBPKA']['char_set'] = 'utf8';
$db['DBPKA']['dbcollat'] = 'utf8_general_ci';
$db['DBPKA']['swap_pre'] = '';
$db['DBPKA']['autoinit'] = TRUE;
$db['DBPKA']['stricton'] = FALSE;


/* Koneksi ke MS Acces SPT */
$db['DBSPT']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM05.mdb';
$db['DBSPT']['username'] = '';
$db['DBSPT']['password'] = '';
$db['DBSPT']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM05.mdb';
$db['DBSPT']['dbdriver'] = 'odbc';
$db['DBSPT']['dbprefix'] = '';
$db['DBSPT']['pconnect'] = TRUE;
$db['DBSPT']['db_debug'] = TRUE;
$db['DBSPT']['cache_on'] = FALSE;
$db['DBSPT']['cachedir'] = '';
$db['DBSPT']['char_set'] = 'utf8';
$db['DBSPT']['dbcollat'] = 'utf8_general_ci';
$db['DBSPT']['swap_pre'] = '';
$db['DBSPT']['autoinit'] = TRUE;
$db['DBSPT']['stricton'] = FALSE;

/* Koneksi ke MS Acces PBUN */
$db['DBPBUN']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM06.mdb';
$db['DBPBUN']['username'] = '';
$db['DBPBUN']['password'] = '';
$db['DBPBUN']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM06.mdb';
$db['DBPBUN']['dbdriver'] = 'odbc';
$db['DBPBUN']['dbprefix'] = '';
$db['DBPBUN']['pconnect'] = TRUE;
$db['DBPBUN']['db_debug'] = TRUE;
$db['DBPBUN']['cache_on'] = FALSE;
$db['DBPBUN']['cachedir'] = '';
$db['DBPBUN']['char_set'] = 'utf8';
$db['DBPBUN']['dbcollat'] = 'utf8_general_ci';
$db['DBPBUN']['swap_pre'] = '';
$db['DBPBUN']['autoinit'] = TRUE;
$db['DBPBUN']['stricton'] = FALSE;

/* Koneksi ke MS Acces MKS */
$db['DBMKS']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM07.mdb';
$db['DBMKS']['username'] = '';
$db['DBMKS']['password'] = '';
$db['DBMKS']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM07.mdb';
$db['DBMKS']['dbdriver'] = 'odbc';
$db['DBMKS']['dbprefix'] = '';
$db['DBMKS']['pconnect'] = TRUE;
$db['DBMKS']['db_debug'] = TRUE;
$db['DBMKS']['cache_on'] = FALSE;
$db['DBMKS']['cachedir'] = '';
$db['DBMKS']['char_set'] = 'utf8';
$db['DBMKS']['dbcollat'] = 'utf8_general_ci';
$db['DBMKS']['swap_pre'] = '';
$db['DBMKS']['autoinit'] = TRUE;
$db['DBMKS']['stricton'] = FALSE;

/* Koneksi ke MS Acces KAYUTANGI */
$db['DB08']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM08.mdb';
$db['DB08']['username'] = '';
$db['DB08']['password'] = '';
$db['DB08']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM08.mdb';
$db['DB08']['dbdriver'] = 'odbc';
$db['DB08']['dbprefix'] = '';
$db['DB08']['pconnect'] = TRUE;
$db['DB08']['db_debug'] = TRUE;
$db['DB08']['cache_on'] = FALSE;
$db['DB08']['cachedir'] = '';
$db['DB08']['char_set'] = 'utf8';
$db['DB08']['dbcollat'] = 'utf8_general_ci';
$db['DB08']['swap_pre'] = '';
$db['DB08']['autoinit'] = TRUE;
$db['DB08']['stricton'] = FALSE;


/* Koneksi ke MS Acces PSB */
$db['DB09']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM09.mdb';
$db['DB09']['username'] = '';
$db['DB09']['password'] = '';
$db['DB09']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM09.mdb';
$db['DB09']['dbdriver'] = 'odbc';
$db['DB09']['dbprefix'] = '';
$db['DB09']['pconnect'] = TRUE;
$db['DB09']['db_debug'] = TRUE;
$db['DB09']['cache_on'] = FALSE;
$db['DB09']['cachedir'] = '';
$db['DB09']['char_set'] = 'utf8';
$db['DB09']['dbcollat'] = 'utf8_general_ci';
$db['DB09']['swap_pre'] = '';
$db['DB09']['autoinit'] = TRUE;
$db['DB09']['stricton'] = FALSE;

/* Koneksi ke MS Acces KM.22 */
$db['DB10']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM10.mdb';
$db['DB10']['username'] = '';
$db['DB10']['password'] = '';
$db['DB10']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM10.mdb';
$db['DB10']['dbdriver'] = 'odbc';
$db['DB10']['dbprefix'] = '';
$db['DB10']['pconnect'] = TRUE;
$db['DB10']['db_debug'] = TRUE;
$db['DB10']['cache_on'] = FALSE;
$db['DB10']['cachedir'] = '';
$db['DB10']['char_set'] = 'utf8';
$db['DB10']['dbcollat'] = 'utf8_general_ci';
$db['DB10']['swap_pre'] = '';
$db['DB10']['autoinit'] = TRUE;
$db['DB10']['stricton'] = FALSE;

/* Koneksi ke MS Acces PS MAS */
$db['DB11']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM12.mdb';
$db['DB11']['username'] = '';
$db['DB11']['password'] = '';
$db['DB11']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM12.mdb';
$db['DB11']['dbdriver'] = 'odbc';
$db['DB11']['dbprefix'] = '';
$db['DB11']['pconnect'] = TRUE;
$db['DB11']['db_debug'] = TRUE;
$db['DB11']['cache_on'] = FALSE;
$db['DB11']['cachedir'] = '';
$db['DB11']['char_set'] = 'utf8';
$db['DB11']['dbcollat'] = 'utf8_general_ci';
$db['DB11']['swap_pre'] = '';
$db['DB11']['autoinit'] = TRUE;
$db['DB11']['stricton'] = FALSE;

/* Koneksi ke MS Acces SNI */
$db['DB12']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM11.mdb';
$db['DB12']['username'] = '';
$db['DB12']['password'] = '';
$db['DB12']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\db_absen\NJRBJM11.mdb';
$db['DB12']['dbdriver'] = 'odbc';
$db['DB12']['dbprefix'] = '';
$db['DB12']['pconnect'] = TRUE;
$db['DB12']['db_debug'] = TRUE;
$db['DB12']['cache_on'] = FALSE;
$db['DB12']['cachedir'] = '';
$db['DB12']['char_set'] = 'utf8';
$db['DB12']['dbcollat'] = 'utf8_general_ci';
$db['DB12']['swap_pre'] = '';
$db['DB12']['autoinit'] = TRUE;
$db['DB12']['stricton'] = FALSE;

/* Koneksi ke MS Acces Surabaya */
$db['SBYMRG'] = array(
    'dsn'	=> '',
    'hostname' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\SBYMRG.mdb',
    'username' => '',
    'password' => '',
    'database' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\SBYMRG.mdb',
    'dbdriver' => 'odbc',
    'dbprefix' => '',
    'pconnect' => TRUE,
    'db_debug' => (ENVIRONMENT !== 'db_akses'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

/* Koneksi ke MS Acces Semarang Demak */
$db['SMGDMK'] = array(
    'dsn'	=> '',
    'hostname' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\SMGDMK.mdb',
    'username' => '',
    'password' => '',
    'database' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\SMGDMK.mdb',
    'dbdriver' => 'odbc',
    'dbprefix' => '',
    'pconnect' => TRUE,
    'db_debug' => (ENVIRONMENT !== 'db_akses'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);


/* Koneksi ke MS Acces Semarang Candi */
$db['SMGCND'] = array(
    'dsn'	=> '',
    'hostname' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\SMGCND.mdb',
    'username' => '',
    'password' => '',
    'database' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\SMGCND.mdb',
    'dbdriver' => 'odbc',
    'dbprefix' => '',
    'pconnect' => TRUE,
    'db_debug' => (ENVIRONMENT !== 'db_akses'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

/* Koneksi ke MS Acces Jakarta */
$db['JKTKPK'] = array(
    'dsn'	=> '',
    'hostname' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\JKTKPK.mdb',
    'username' => '',
    'password' => '',
    'database' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\JKTKPK.mdb',
    'dbdriver' => 'odbc',
    'dbprefix' => '',
    'pconnect' => TRUE,
    'db_debug' => (ENVIRONMENT !== 'db_akses'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

/* Koneksi ke MS Acces Sukoharjo */
$db['SKHRJ'] = array(
    'dsn'	=> '',
    'hostname' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\SKHRJ.mdb',
    'username' => '',
    'password' => '',
    'database' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\SKHRJ.mdb',
    'dbdriver' => 'odbc',
    'dbprefix' => '',
    'pconnect' => TRUE,
    'db_debug' => (ENVIRONMENT !== 'db_akses'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

/* Koneksi ke MS Acces JOGJA */
$db['JOG'] = array(
    'dsn'	=> '',
    'hostname' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\JOG.mdb',
    'username' => '',
    'password' => '',
    'database' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\JOG.mdb',
    'dbdriver' => 'odbc',
    'dbprefix' => '',
    'pconnect' => TRUE,
    'db_debug' => (ENVIRONMENT !== 'db_akses'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

/* Koneksi ke MS Acces Rembang */
$db['RMBG'] = array(
    'dsn'	=> '',
    'hostname' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\RMBG.mdb',
    'username' => '',
    'password' => '',
    'database' => 'Driver={Microsoft Access Driver (*.mdb)};DBQ=D:\WEB\apache2\htdocs\hrms.nusa\assets\db_absen\RMBG.mdb',
    'dbdriver' => 'odbc',
    'dbprefix' => '',
    'pconnect' => TRUE,
    'db_debug' => (ENVIRONMENT !== 'db_akses'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

/* Koneksi ke MS Acces Surabaya
$db['SBYMRG']['hostname'] = "Driver={Microsoft Access Driver (*.mdb)};DBQ=E:\WEBNUSA\apache2\htdocs\hrdnew\assets\db_absen\SBYMRG.mdb";
//$db['SBYMRG']['hostname'] = 'sby_lokal';
$db['SBYMRG']['username'] = '';
$db['SBYMRG']['password'] = '';
$db['SBYMRG']['database'] = "Driver={Microsoft Access Driver (*.mdb)};DBQ=E:\WEBNUSA\apache2\htdocs\hrdnew\assets\db_absen\SBYMRG.mdb";
//$db['SBYMRG']['database'] = 'sby_lokal';
$db['SBYMRG']['dbdriver'] = 'odbc';
$db['SBYMRG']['dbprefix'] = '';
$db['SBYMRG']['pconnect'] = TRUE;
$db['SBYMRG']['db_debug'] = TRUE;
$db['SBYMRG']['cache_on'] = FALSE;
$db['SBYMRG']['cachedir'] = '';
$db['SBYMRG']['char_set'] = 'utf8';
$db['SBYMRG']['dbcollat'] = 'utf8_general_ci';
$db['SBYMRG']['swap_pre'] = '';
$db['SBYMRG']['autoinit'] = TRUE;
$db['SBYMRG']['stricton'] = FALSE;
*/
/* Koneksi ke MS Acces Semarang Demak
$db['SMGDMK']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=E:\WEBNUSA\apache2\htdocs\hrdnew\assets\db_absen\SMGDMK.mdb';
$db['SMGDMK']['username'] = '';
$db['SMGDMK']['password'] = '';
$db['SMGDMK']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=E:\WEBNUSA\apache2\htdocs\hrdnew\assets\db_absen\SMGDMK.mdb';
$db['SMGDMK']['dbdriver'] = 'odbc';
$db['SMGDMK']['dbprefix'] = '';
$db['SMGDMK']['pconnect'] = TRUE;
$db['SMGDMK']['db_debug'] = TRUE;
$db['SMGDMK']['cache_on'] = FALSE;
$db['SMGDMK']['cachedir'] = '';
$db['SMGDMK']['char_set'] = 'utf8';
$db['SMGDMK']['dbcollat'] = 'utf8_general_ci';
$db['SMGDMK']['swap_pre'] = '';
$db['SMGDMK']['autoinit'] = TRUE;
$db['SMGDMK']['stricton'] = FALSE;
*/
/* Koneksi ke MS Acces Semarang Candi
$db['SMGCND']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=E:\WEBNUSA\apache2\htdocs\hrdnew\assets\db_absen\SMGCND.mdb';
$db['SMGCND']['username'] = '';
$db['SMGCND']['password'] = '';
$db['SMGCND']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=E:\WEBNUSA\apache2\htdocs\hrdnew\assets\db_absen\SMGCND.mdb';
$db['SMGCND']['dbdriver'] = 'odbc';
$db['SMGCND']['dbprefix'] = '';
$db['SMGCND']['pconnect'] = TRUE;
$db['SMGCND']['db_debug'] = TRUE;
$db['SMGCND']['cache_on'] = FALSE;
$db['SMGCND']['cachedir'] = '';
$db['SMGCND']['char_set'] = 'utf8';
$db['SMGCND']['dbcollat'] = 'utf8_general_ci';
$db['SMGCND']['swap_pre'] = '';
$db['SMGCND']['autoinit'] = TRUE;
$db['SMGCND']['stricton'] = FALSE;
 */
/* Koneksi ke MS Acces Jakarta
$db['JKTKPK']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=E:\WEBNUSA\apache2\htdocs\hrdnew\assets\db_absen\JKTKPK.mdb';
$db['JKTKPK']['username'] = '';
$db['JKTKPK']['password'] = '';
$db['JKTKPK']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=E:\WEBNUSA\apache2\htdocs\hrdnew\assets\db_absen\JKTKPK.mdb';
$db['JKTKPK']['dbdriver'] = 'odbc';
$db['JKTKPK']['dbprefix'] = '';
$db['JKTKPK']['pconnect'] = TRUE;
$db['JKTKPK']['db_debug'] = TRUE;
$db['JKTKPK']['cache_on'] = FALSE;
$db['JKTKPK']['cachedir'] = '';
$db['JKTKPK']['char_set'] = 'utf8';
$db['JKTKPK']['dbcollat'] = 'utf8_general_ci';
$db['JKTKPK']['swap_pre'] = '';
$db['JKTKPK']['autoinit'] = TRUE;
$db['JKTKPK']['stricton'] = FALSE;
*/
/* Koneksi ke MS Acces Jakarta
$db['SKHRJ']['hostname'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=E:\WEBNUSA\apache2\htdocs\hrdnew\assets\db_absen\SKHRJ.mdb';
$db['SKHRJ']['username'] = '';
$db['SKHRJ']['password'] = '';
$db['SKHRJ']['database'] = 'Driver={Microsoft Access Driver (*.mdb)};DBQ=E:\WEBNUSA\apache2\htdocs\hrdnew\assets\db_absen\SKHRJ.mdb';
$db['SKHRJ']['dbdriver'] = 'odbc';
$db['SKHRJ']['dbprefix'] = '';
$db['SKHRJ']['pconnect'] = TRUE;
$db['SKHRJ']['db_debug'] = TRUE;
$db['SKHRJ']['cache_on'] = FALSE;
$db['SKHRJ']['cachedir'] = '';
$db['SKHRJ']['char_set'] = 'utf8';
$db['SKHRJ']['dbcollat'] = 'utf8_general_ci';
$db['SKHRJ']['swap_pre'] = '';
$db['SKHRJ']['autoinit'] = TRUE;
$db['SKHRJ']['stricton'] = FALSE;
*/

//Koneksi ke Postgre
//$db['default']['hostname'] = '25.33.133.115';
/*
$db['default']['hostname'] = '192.168.15.4';
$db['default']['username'] = 'postgres';
$db['default']['password'] = '111111';
$db['default']['database'] = 'HRDNUSANEW';
$db['default']['dbdriver'] = 'postgre';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;
*/

$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'postgres';
$db['default']['password'] = '@hC1_5*HrMs4%8';
$db['default']['database'] = 'HRMS.NSNJRM';
$db['default']['dbdriver'] = 'postgre';
$db['default']['port'] = 17296;
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;
/* //Koneksi ke Postgre server
$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'postgres';
$db['default']['password'] = 'ROOT';
$db['default']['database'] = 'HRDNUSANEW';
$db['default']['dbdriver'] = 'postgre';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;*/
 


/* End of file database.php */
/* Location: ./application/config/database.php */

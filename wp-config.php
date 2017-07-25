<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'euroroaming');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'hDa$i6gbO8(eMr9z_8It:WrV4vub(kZa,c;Z%Jdk74GA6Cwyc =Z]vX?$e?D&!;p');
define('SECURE_AUTH_KEY',  '{=lv_d|c^V,[oTF6(!o~OT;?&6st5B:FZ^;]#SZx)M>n)~y -voklvX}i[2z@}2K');
define('LOGGED_IN_KEY',    '_#y`og8%9:m&wA(oHK:g74}AnN>#s_6{MIM;=O1lzV{neVR/(+gWR&*l*~p<w$WK');
define('NONCE_KEY',        'Vzxl/56HvU*gk6X~Bulh7YZ<0 Sfp-4/NUJ[3!tTAp_ZXy@DPAoHBBIqt 5vJk*t');
define('AUTH_SALT',        '@Ew^Jxt~slUn.!:<1Q|Ud96D r/1AOX91/p6!$E[i|G ,VnCnakQuHI#I,pQfBeU');
define('SECURE_AUTH_SALT', 'Pu_}D8fSx7(X$Y*MBmBTngSe1:_bH!n!9*D;h!{Sh-WW|Jid`%R[Cl.;V^B[<-;l');
define('LOGGED_IN_SALT',   'G(kjq$5+Ra_`lZ<16?{&>([SI*spvt d`tzF1,*2s[Sa5_&Qe{L|t?#%kVzxuZxv');
define('NONCE_SALT',       'gCH;Wx3FNzU6ka1LwH ENMOv76h*}kEZ)Ek@!,(#:HS<@J<}{BGBt{-gW|%Gia8|');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 * 
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
//define('WP_DEBUG', false);
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG_LOG', true );
define ('WPLANG', 'ru_RU');


/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');

<?PHP
/**
 * DB_CONNECT(DB接続)
 *
 * @return $DB
 */
function DB_CONNECT()
{
    // *************
    // * DB関連定数 *
    // *************
    $DB_HOST = "example.jp";
    $DB_USER = "db_user";
    $DB_PASS = "password";
    $DB_NAME = "db_name";

    // データベースに接続
    if (!($DB = mysql_connect($DB_HOST, $DB_USER, $DB_PASS))) {
        die('DB Connect Err:'.mysql_error());
    }

    // データベースの選択
    mysql_select_db($DB_NAME,$DB);

    // 文字コードの指定
    DB_CHARSET($DB);

    // DBオブジェクトを返す
    return $DB;
}

/**
 * DB_CHARSET(文字エンコード指定)
 *
 * @param $DB
 * @return void
 */
function DB_CHARSET($DB)
{
    if (!(mysql_set_charset('utf8',$DB))) {
        return false;
    } else {
        return true;
    }
}
/**
 * DB_EXE(クエリ実行)
 *
 * @param $DB
 * @param string $sql
 * @return void
 */
function DB_EXE($DB,$sql)
{
    global $G_QUERY;
    $G_QUERY = mysql_query($sql, $DB);
    if(!$G_QUERY){
        $message  = 'Invalid query:' . mysql_error() . "\n";
        $message .= 'Whole query ' . $query;
        die($message);
    }
}
/**
 * DB_CLOSE(DB切断)
 *
 * @param $DB
 * @return void
 */
function DB_CLOSE($DB)
{
	mysql_close($DB);
}

/**
 * DB_BEGIN_TRANSACTION(トランザクション開始)
 *
 * @param $DB
 * @return void
 */
function DB_BEGIN_TRANSACTION($DB)
{
    $G_QUERY = null;
    $sql = "SET AUTOCOMMIT = 0";
    DB_EXE($DB, $sql);
    $G_QUERY = null;
    $sql = "BEGIN";
    DB_EXE($DB, $sql);
}

/**
 * DB_COMMIT(コミット)
 *
 * @param $DB
 * @return void
 */
function DB_COMMIT($DB)
{
    $G_QUERY = null;
    $sql = "COMMIT";
    DB_EXE($DB, $sql);
}

/**
 * DB_ROLLBACK(ロールバック)
 *
 * @param $DB
 * @return void
 */
function DB_ROLLBACK($DB)
{
    $G_QUERY = NULL;
    $sql = "ROLLBACK";
    DB_EXE($DB, $sql);
}

/**
 * DB_END_TRANSACTION(トランザクション終了)
 *
 * @param $DB
 * @param bool $ret
 * @return void
 */
function DB_END_TRANSACTION($DB, $ret)
{
    if ($ret == true) {
        DB_COMMIT($DB);
    } else {
        DB_ROLLBACK($DB);
    }
}

/**
 * DB_TABLE_LOCK(テーブルロック)
 * (ロックの種類)											*
 * READ:SELECT以外できなくなる								*
 * READ LOCAL:ロックをかけたクライアントだけSELECT以外できなくなる		*
 * WRITE:ロックをかけたクライアント以外は全て操作できなくなる			*
 *
 * @param $DB
 * @param string $TableName
 * @return void
 */
function DB_TABLE_LOCK($DB, $TableName)
{
    $G_QUERY =  null;
    $sql = "LOCK TABLES {$TableName} READ";
    DB_EXE($DB, $sql);
}

/**
 * DB_TABLE_UNLOCK(テーブルロックの解除)
 *
 * @param $DB
 * @param string $TableName
 * @return void
 */
function DB_TABLE_UNLOCK($DB, $TableName)
{
    $G_QUERY = NULL;
    $sql = "UNLOCK {$TableName}";
    DB_EXE($DB, $sql);
}

/**
 * DB_REC_GET(次のレコードを取得)
 *
 * @param $G_QUERY
 * @return $G_REC
 */
function DB_REC_GET($G_QUERY)
{
    $G_REC = @mysql_fetch_array($G_QUERY, MYSQL_ASSOC);
    return $G_REC;
}

/**
 * DB_QUERY_COUNT(レコード件数を取得)
 *
 * @param $G_QUERY
 * @return int
 */
function DB_QUERY_COUNT($G_QUERY)
{
    return @mysql_num_rows($G_QUERY);
}
?>

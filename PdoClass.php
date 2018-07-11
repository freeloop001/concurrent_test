<?php
$pdo = new PdoClass();
/*
 *
 * 说明：所有方法均返回数据，样式如下：
 * Array ( [errcode] => * [errmsg] => *** [errtime] => 2017-03-16 10:22:24 )
 * 其中errcode值为0：失败，1：成功，使用时判断这个值就可以。
 * errmsg：返回值，失败时返回错误信息，成功时返回正确的结果（数组或者字符串）
 *
 * 使用参数获取单条数据 ###################
 * $rows = $pdo->FetchRow("select * from boc_wxuser where `id`=:id or userid=:userid  ",array('id'=>'1','userid'=>'2463720'));
 * print_r($rows);
 * 说明：如果没有参数，直接省略第二个参数，如下所示：
 * $rows = $pdo->FetchRow("select * from boc_wxuser );

 * 获取多条数据
 * $rows = $pdo->FetchAll("select * from boc_wxuser where userid=:userid limit 10 ",array('userid'=>'2463720'));
 * print_r($rows);
 * 说明：如果没有参数，直接省略第二个参数

 * 使用事务更新数据
 * $rows = $pdo->PtmTstQuery("update boc_wxuser set duty='在岗工作' where `id`=:id  ",array('id'=>'1'));

 * 使用事务更新多条语句
 * $pdo->Begin();
 * $rows = $pdo->PtmQuery("update boc_wxuser set duty='在岗工作' where `id`=:id  ",array('id'=>'1'));
 * print_r($rows);
 * $rows1 = $pdo->PtmQuery("update boc_wxuser set duty='在岗工作' where `id`=:id  ",array('id'=>'-1'));
 * print_r($rows1);
 * if($row['errcode']&&$rows1['errcode'])
 *      $pdo->Commit();
 * else
 *    $pdo->RollBack();
 *
 */

class PdoClass
{

    public $dbh         = ""; //全局连接Object;
    private $dsn        = 'mysql:host=localhost;dbname=test';
    private $user       = "root";
    private $password   = "root";
    public $returnArray = array('errcode' => '', 'errmsg' => '');

    //构造函数 - 初始化连接
    public function __construct()
    {
        $this->pdoConnect();
    }

    private function pdoConnect()
    {
        try {
            $this->dbh = new PDO($this->dsn, $this->user, $this->password); //,array(PDO::ATTR_PERSISTENT => true)长连接设置，是否有用？
            return $this->dbh;
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            exit();
        }
    }

    //设置PDO参数
    public function Attribute($attribute, $value)
    {
        $this->dbh->setAttribute($attribute, $value);
    }

    public function LastId($name = null)
    {
        return $this->dbh->lastInsertId($name);
    }

    //数据库单语句执行操作
    public function Exec($param)
    {
        try {
            $rows = $this->dbh->exec($param); //影响行数
            return $this->Log(true, $rows);
        } catch (PDOException $e) {
            return $this->Log(false, $e->getMessage());
        }
    }

    //格式化数据
    public function Quote($string)
    {
        return $this->dbh->quote($string);
    }

    //批量处理格式化数据
    public function BatchQuote($data)
    {
        $result = null;
        if (!empty($data) && (is_array($data) || is_object($data))) {
            foreach ($data as $key => $value) {
                if (!empty($value) && (is_array($value) || is_object($value))) {
                    $result[$key] = $this->BatchQuote($value);
                } else {
                    $result[$key] = $this->Quote($value);
                }
            }
        } else {
            $result = $this->Quote($data);
        }
        //print_r($result);
        return $result;
    }

    //数据库预处理操作 - 获取全部数据
    public function FetchAll($statement, $parameter = null, $type = PDO::FETCH_ASSOC)
    {
        try {
            $sth = $this->dbh->prepare($statement);
            $sth->execute($parameter);
            //$sth->execute($this->BatchQuote($parameter));
            $result = $sth->fetchAll($type);
            if (!empty($result) && is_array($result)) {
                return $this->Log(true, $result);
            } else {
                return $this->Log(true, null);
            }
        } catch (PDOException $e) {
            return $this->Log(false, $e->getMessage());
        }
    }

    //数据库预处理操作 - 获取一行数据
    public function FetchRow($statement, $parameter = null, $type = PDO::FETCH_ASSOC)
    {
        try {
            $sth = $this->dbh->prepare($statement);
            $sth->execute($parameter);
            $result = $sth->fetch($type);
            if (!empty($result) && is_array($result)) {
                return $this->Log(true, $result);
            } else {
                return $this->Log(true, null);
            }
        } catch (PDOException $e) {
            return $this->Log(false, $e->getMessage());
        }
    }

    //数据库预处理操作 - 获取一个数据
    public function FetchOne($statement, $parameter = null)
    {
        try {
            $sth = $this->dbh->prepare($statement);
            $sth->execute($parameter);
            $result = $sth->fetch(PDO::FETCH_NUM);
            if (!empty($result) && is_array($result)) {
                return $this->Log(true, $result[0]);
            } else {
                return $this->Log(true, null);
            }
        } catch (PDOException $e) {
            return $this->Log(false, $e->getMessage());
        }
    }

    //开始事务
    public function Begin()
    {
        $this->dbh->beginTransaction();
    }

    //提交事务
    public function Commit()
    {
        $this->dbh->commit();
    }

    //回滚事务
    public function RollBack()
    {
        $this->dbh->rollBack();
    }

    //预处理事务执行语句
    public function PtmTstQuery($statement, $parameter = null)
    {
        try {
            $this->Begin();
            $result = $this->dbh->prepare($statement)->execute($parameter);
            $this->Commit();
            return $this->Log(true, $result);
        } catch (PDOException $e) {
            $this->RollBack();
            return $this->Log(false, $e->getMessage());
        }
    }

    //预处理执行语句
    public function PtmQuery($statement, $parameter = null)
    {
        try {
            $sth = $this->dbh->prepare($statement);
            $sth->execute($parameter);
            $result = $sth->rowCount();
            return $this->Log(true, $result);
        } catch (PDOException $e) {
            return $this->Log(false, $e->getMessage());
        }
    }

    //Query执行
    public function Query($statement, $type = PDO::FETCH_ASSOC)
    {
        try {
            $result = $this->dbh->query($statement, $type);
            return $this->Log(true, $result);
        } catch (PDOException $e) {
            return $this->Log(false, $e->getMessage());
        }
    }

    //日志LOG
    public function Log($errcode, $errmsg)
    {
        $this->returnArray            = array();
        $this->returnArray['errcode'] = $errcode;
        $this->returnArray['errmsg']  = $errmsg;
        $this->returnArray['errtime'] = date("Y-m-d H:i:s", time());
        return $this->returnArray;
    }
}

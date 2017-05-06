<?php
require_once('BaseModel.php');

class BnhanModel extends BaseModel
{
	public function getBNhanByRoom($room){
        $date = date('d-m-Y',time());
        $startTime = strtotime($date);
        $endTime = $startTime + 86000; 
		$db = $this->getSlaveDb();
		$sql = "SELECT bq.*,b.NGAY_SINH,b.GIOI_TINH FROM BENH_NHAN_QUEUE as bq INNER JOIN BENH_NHAN as b ON bq.BENH_NHAN_ID = b.BENH_NHAN_ID WHERE bq.room = ? AND bq.created >= $startTime AND bq.created < $endTime GROUP BY b.tc ORDER BY bq.sort ASC";
		//echo $sql;exit;
        return $db->fetchAll($sql,$room);
	}
	public function updateSort($sort,$BENH_NHAN_ID){
		$db = $this->getMasterDb();
		$sql = "UPDATE BENH_NHAN_QUEUE SET sort = ? WHERE BENH_NHAN_ID = ?";
		$db->query($sql,array($sort,$BENH_NHAN_ID));
	}
    public function updateRoom($room,$sort,$BENH_NHAN_ID){
        $db = $this->getMasterDb();
        $sql = "UPDATE BENH_NHAN_QUEUE SET ROOM = ?,sort = ? WHERE id = ?";
        $db->query($sql,array($room,$sort,$BENH_NHAN_ID));
    }
	public function getSTTNow($room){
		$db = $this->getSlaveDb();
		$sql = "SELECT * FROM BENH_NHAN_QUEUE WHERE room = ? ORDER BY sort DESC LIMIT 0,1";
		return $db->fetchRow($sql,$room);
	}
	public function searchBNhan($search, $page = 1){
		if(!$page > 0){
            $page = 1;
        }
        $limit = ' LIMIT ' . (($page - 1)*COUNT_ITEMS_PER_PAGE) . ', ' . (COUNT_ITEMS_PER_PAGE + 1);

        $where = "";
        $params = array();
        if(isset($search['TEN_BNHAN']) && $search['TEN_BNHAN'] != ''){
            $where .= $where==""?" WHERE TEN_BNHAN LIKE ?":" AND TEN_BNHAN LIKE ?";
            $params[] = "%".$search['TEN_BNHAN']."%";
        }
        if(isset($search['tc']) && $search['tc'] != ''){
            $where .= $where==""?" WHERE tc = ?":" AND tc = ?";
            $params[] = $search['tc'];
        }
        if(isset($search['status']) && $search['status'] >= 0){
            $where .= $where==""?" WHERE status = ?":" AND status = ?";
            $params[] = $search['status'];
        }

        $sql = "SELECT * FROM BENH_NHAN";
        $order = " GROUP BY tc ORDER BY room ASC";

        $sql = $sql . $where . $order . $limit;

        $db = $this->getSlaveDb();
        return $db->fetchAll($sql, $params);
	}
}
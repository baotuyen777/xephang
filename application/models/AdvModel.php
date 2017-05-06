<?php

require_once('BaseModel.php');

class AdvModel extends BaseModel {

    private $table = 'adv';

    public function getAll() {
        $db = $this->getSlaveDb();
        $sql = "SELECT * from " . $this->table . " ORDER BY orders ASC";
        return $db->fetchAll($sql);
    }

    public function getRow($id) {
        $db = $this->getSlaveDb();
        $sql = "SELECT * from " . $this->table ;
        return $db->fetchRow($sql);
    }

    public function add($data) {
        return $this->insertToTable($this->table, $data);
    }

    public function delete($id) {
        $db = $this->getSlaveDb();
        $sql = "DELETE from `" . $this->table . "` WHERE `id` = ?";
        var_dump($id);
        return $db->query($sql, array($id));
    }

    public function updateAdvSetting($arrAllKey) {
        $db = $this->getMasterDb();
        foreach ($arrAllKey as $arrSingleKey) {
            if (!$db->fetchOne("SELECT `key` from `option` where `key`='" . $arrSingleKey['key'] . "'")) {
                $this->addOption($arrSingleKey);
//                echo  $arrSingleKey['key'];
            } else {
                $sql = "UPDATE `option` SET `value` = ? WHERE `key` = ?";
                $db->query($sql, array($arrSingleKey['value'], $arrSingleKey['key']));
            }
        }
    }

    public function addOption($data) {
        return $this->insertToTable('`option`', $data);
    }

    public function getOptionByKey($key) {
        $db = $this->getSlaveDb();
        $sql = "SELECT `value` from `option` where `key`='" . $key . "'";
        return $db->fetchOne($sql);
    }

    public function getAllOption() {
        $db = $this->getSlaveDb();
        $sql = "SELECT * from `option`";
        return $db->fetchAll($sql);
    }

}

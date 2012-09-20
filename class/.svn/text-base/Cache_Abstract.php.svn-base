<?php
  abstract class Cache_Abstract{
     public function set($id, $value, $duration=0) {}

        public function get($id) {}

        public function flush($id) {}

        public function flushAll() {}

        protected function generateKey($id) {
                return $id !== null ? md5($id) : false;
        }
  }
?>

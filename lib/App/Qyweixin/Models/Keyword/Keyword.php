<?php

namespace App\Qyweixin\Models\Keyword;

class Keyword extends \App\Common\Models\Qyweixin\Keyword\Keyword
{
    /**
     * 获取符合指定类型的关键词列表
     *
     * @param bool $fuzzy            
     * @return array
     */
    public function getKeywordByType($authorizer_appid, $provider_appid, $agentid, $fuzzy)
    {
        $keywordList = array();
        $cacheKey = "keyword:authorizer_appid:{$authorizer_appid}:provider_appid:{$provider_appid}:agentid:{$agentid}:fuzzy:{$fuzzy}";
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $cacheKey);
        $cache = $this->getDI()->get('cache');
        $keywordList = $cache->get($cacheKey);
        if (empty($keywordList)) {
            $rst = $this->getListByFuzzy($authorizer_appid, $provider_appid, $agentid, $fuzzy);
            $keywordList = array();
            if (!empty($rst)) {
                foreach ($rst as $row) {
                    $keywordList[$this->keyword2lower($row['keyword'])] = $row;
                }
            }
            if (!empty($keywordList)) {
                // 加缓存处理
                $expire_time = 1 * 60; // 1分钟
                $cache->save($cacheKey, $keywordList, $expire_time);
            }
        }
        return $keywordList;
    }

    public function matchKeyWord($msg, $authorizer_appid, $provider_appid, $agentid, $fuzzy = false)
    {
        $msg = trim($msg);
        $keywordList = $this->getKeywordByType($authorizer_appid, $provider_appid, $agentid, $fuzzy);
        if (!$fuzzy) {
            $msg = strtolower($msg);
            if (isset($keywordList[$msg])) {
                $this->incHitNumber($keywordList[$msg]['_id']);
                return $keywordList[$msg];
            } else {
                return $this->matchKeyWord($msg, $authorizer_appid, $provider_appid, $agentid, true);
            }
        } else {
            $split = $this->split($msg, 1, 10);
            $keys = array();
            if (count($split) > 0) {
                $keys = array_keys($keywordList);
                $keys = array_intersect($split, $keys);
            }
            if (count($keys) == 0) {
                return array();
            }

            $queue = new KeywordPriorityQueue();
            foreach ($keys as $key) {
                $queue->insert($keywordList[$key], $keywordList[$key]['priority']);
            }
            $queue->top();

            $result = $queue->current();
            $this->incHitNumber($result['_id']);
            return $result;
        }
    }

    /**
     * 对于文本内容进行一元拆分，但是保留完整的英文单词、网址、电子邮箱、数字信息,注意不区分大小写，全部转换为小写进行匹配
     *
     * @param string $str            
     * @return array
     */
    private function match($str)
    {
        $str = strtolower(trim($str));
        if (preg_match_all("/(?:[a-z'\-\.\/\:_@0-9#\?\!\,\;]+|[\x80-\xff]{3})/i", $str, $match)) {
            return $match[0];
        }
        return array();
    }

    /**
     * 对于分词结果进行$elementMin元至$elementMax元的分词组合
     *
     * @param string $str
     *            字符串
     * @param int $elementMin
     *            最小分词元数
     * @param int $elementMax
     *            最大分词元数
     * @return array
     */
    public function split($str, $elementMin = 1, $elementMax = 0)
    {
        $elementMin = (int) $elementMin;
        $elementMax = (int) $elementMax;
        $elements = $this->match($str);
        $elementsNumber = count($elements);
        if ($elementsNumber == 0)
            return array();

        $elementMin = $elementMin <= 0 ? 1 : $elementMin;
        $elementMax = $elementMax == 0 ? $elementsNumber : $elementMax;
        $elementMax = $elementMax > $elementsNumber ? $elementsNumber : $elementMax;
        $elementMax = $elementMin > $elementMax ? $elementMin : $elementMax;

        $arrSplit = array();
        do {
            foreach ($elements as $key => $element) {
                if ($elementsNumber >= $key + $elementMin)
                    $arrSplit[] = implode(array_slice($elements, $key, $elementMin));
            }
            $elementMin += 1;
        } while ($elementMin <= $elementMax);
        return $arrSplit;
    }

    /**
     * 记录关键词的命中次数
     */
    public function incHitNumber($id)
    {
        $updateData = array();
        $updateData['times'] = 1;
        return $this->update(array('_id' => $id), array('$inc' => $updateData));
    }

    /**
     * 将关键词中的大写字母转化为小写
     *
     * @param string $str            
     */
    public function keyword2lower($str)
    {
        // return strtolower($str);
        return preg_replace_callback('/[A-Z]/', function ($matches) {
            return strtolower($matches[0]);
        }, $str);
    }

    public function getListByFuzzy($authorizer_appid, $provider_appid, $agentid, $fuzzy)
    {
        $is_fuzzy = intval($fuzzy);
        $ret = $this->findAll(array(
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'provider_appid' => $provider_appid,
            'is_fuzzy' => (empty($is_fuzzy) ? false : true)
        ), array('priority' => -1, '_id' => -1));
        return $ret;
    }
}

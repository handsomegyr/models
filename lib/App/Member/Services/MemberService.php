<?php

namespace App\Member\Services;

class MemberService
{
    // 报名
    public const ACT_TYPE_BM = 1;
    // 有介(邀请好友)
    public const ACT_TYPE_YJ = 2;
    // 心愿单
    public const ACT_TYPE_XYD = 3;
    // 收藏
    public const ACT_TYPE_SC = 4;
    // 成就
    public const ACT_TYPE_CJ = 5;
    // 兑换
    public const ACT_TYPE_DH = 6;
    // 签到
    public const ACT_TYPE_QD = 7;
    // 阅读文章
    public const ACT_TYPE_YDWZ = 8;
    // 观看视频
    public const ACT_TYPE_GKSP = 9;
    // 浏览产品
    public const ACT_TYPE_LOOK = 10;
    // 邀请好友报名 INVITOR
    public const ACT_TYPE_INVITOR = 11;
    // 我的客户，销售用
    public const ACT_TYPE_CUSTOMER = 12;
    /**
     * @var \App\Member\Models\BehaviorLog
     */
    private $modelMemberBehaviorLog = null;

    /**
     * @var \App\Member\Models\BehaviorStat
     */
    private $modelMemberBehaviorStat = null;

    /**
     * @var \App\Member\Models\BehaviorDailyStat
     */
    private $modelMemberBehaviorDailyStat = null;

    /**
     * @var \App\Task\Models\Task
     */
    private $modelTask = null;

    /**
     * @var \App\Member\Models\TaskLog
     */
    private $modelMemberTaskLog = null;

    /**
     * @var \App\Points\Models\Rule
     */
    private $modelPointRule = null;

    /**
     * @var \App\Points\Models\User
     */
    private $modelPointUser = null;

    /**
     * @var \App\Points\PointService
     */
    private $servicePoint = null;

    /**
     * @var \App\Exchange\Models\Exchange
     */
    private $modelExchange = null;

    public function __construct()
    {
        $this->modelMemberBehaviorLog = new \App\Member\Models\BehaviorLog();
        $this->modelMemberBehaviorStat = new \App\Member\Models\BehaviorStat();
        $this->modelMemberBehaviorDailyStat = new \App\Member\Models\BehaviorDailyStat();
        $this->modelTask = new \App\Task\Models\Task();
        $this->modelMemberTaskLog = new \App\Member\Models\Task();
        $this->modelPointRule = new \App\Points\Models\Rule();
        $this->modelPointUser = new \App\Points\Models\User();
        $this->servicePoint = new \App\Points\Services\PointService();
        $this->modelExchange = new \App\Exchange\Models\Exchange();
    }

    /**
     * 记录行为日志
     */
    public function logBehavior(
        $act_type,
        $act_num,
        $act_daily_num,
        $member_id,
        $mobile,
        $openid,
        $name,
        $headimgurl,
        $page_url,
        $scene,
        $btn_name,
        $ip,
        $act_time,
        $act_content_type,
        $act_content_subtype,
        $act_content_id,
        $activity_id,
        $invitor,
        array $memo = array('memo' => '')
    ) {
        $act_num = intval($act_num);
        // 如果行为次数是增加的
        if ($act_num > 0) {
            // 记录行为日志记录
            $behaviorLogInfo = $this->modelMemberBehaviorLog->log(
                $act_type,
                $member_id,
                $mobile,
                $openid,
                $page_url,
                $scene,
                $btn_name,
                $ip,
                $act_time,
                $act_content_type,
                $act_content_subtype,
                $act_content_id,
                $activity_id,
                $invitor,
                $memo
            );
            // 做每日统计行为日志
            $behaviorDailyStatInfo = $this->modelMemberBehaviorDailyStat->logStat(
                $act_type,
                $member_id,
                $mobile,
                $openid,
                $act_num,
                $act_time,
                $act_content_type,
                $act_content_subtype,
                $act_content_id,
                $act_daily_num
            );
            // 做统计行为日志
            $behaviorStatInfo = $this->modelMemberBehaviorStat->logStat(
                $act_type,
                $member_id,
                $mobile,
                $openid,
                $act_num,
                $act_time,
                $act_daily_num
            );
            // 检查是否是任务相关的行为
            $isTaskBehavior = $this->isTaskBehavior($act_type);
            // 如果是任务相关的行为
            if ($isTaskBehavior) {

                // 检查是否达到了完成任务的条件并返回列表
                $completeTaskList = $this->modelTask->getCompleteTaskList(
                    $act_type,
                    $act_time,
                    $behaviorStatInfo['total_complete_num']
                );

                // 如果task_id是有值的话 说明可以完成任务
                if (!empty($completeTaskList)) {
                    foreach ($completeTaskList as $taskInfo) {
                        // 检查是否已完成了任务
                        $memberTaskLogInfo = $this->modelMemberTaskLog->getInfoByMobile($mobile, $taskInfo['id']);

                        if (empty($memberTaskLogInfo)) {
                            // 记录完成任务的日志
                            $memberTaskLogInfo = $this->modelMemberTaskLog->log(
                                $taskInfo['id'],
                                $member_id,
                                $mobile,
                                $openid,
                                $act_time,
                                array('taskInfo' => $taskInfo, 'behaviorLogInfo' => $behaviorLogInfo)
                            );
                            // 任务完成数量+1
                            $query = array(
                                '_id' => $taskInfo['_id']
                            );
                            $updateData = array(
                                '$inc' => array(
                                    'complete_num' => 1
                                )
                            );
                            $this->modelTask->update($query, $updateData);

                            // 如果配置了任务达成后的礼物 那么进行礼物的处理
                            if (!empty($taskInfo['gifts']['pointRuleList'])) {
                                foreach ($taskInfo['gifts']['pointRuleList'] as $giftItem) {

                                    $pointRuleInfo = $this->modelPointRule->getInfoById($giftItem);
                                    if (empty($pointRuleInfo)) {
                                        throw new \Exception("rule_id:{$giftItem}所对应的徽章规则未设置", -499996);
                                    }
                                    $uniqueId = "{$mobile}_{$act_type}_{$taskInfo['id']}_{$pointRuleInfo['id']}";
                                    $pointUserInfo = $this->addOrReducePoint(
                                        $member_id,
                                        $mobile,
                                        $openid,
                                        $name,
                                        $headimgurl,
                                        $pointRuleInfo,
                                        $act_time,
                                        $uniqueId,
                                        $activity_id,
                                        $scene,
                                        null,
                                        array('taskInfo' => $taskInfo, 'behaviorLogInfo' => $behaviorLogInfo)
                                    );

                                    // 如果成功的话
                                    if (!empty($pointUserInfo)) {
                                        // 做统计行为日志
                                        $behaviorStatInfo = $this->modelMemberBehaviorStat->logStat(
                                            self::ACT_TYPE_CJ,
                                            $member_id,
                                            $mobile,
                                            $openid,
                                            $pointUserInfo['got_points'],
                                            $act_time,
                                            $pointUserInfo['got_points']
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            // 如果行为类型是收藏的
            if ($act_type == self::ACT_TYPE_SC) {
                // 删除收藏记录
                $this->modelMemberBehaviorLog->forceDelete($mobile, $act_type, $act_content_type, $act_content_subtype, $act_content_id);
                // 更新收藏的行为数据记录
                $this->modelMemberBehaviorStat->logStat(
                    $act_type,
                    $member_id,
                    $mobile,
                    $openid,
                    $act_num,
                    $act_time,
                    $act_daily_num
                );
            }
        }
    }

    // 是否是任务相关的行为
    protected function isTaskBehavior($act_type)
    {
        $isTaskBehavior = false;
        if (in_array(intval($act_type), array(
            // 报名
            self::ACT_TYPE_BM,
            // 友介
            self::ACT_TYPE_YJ,
            // 签到
            self::ACT_TYPE_QD,
            // 阅读文章
            self::ACT_TYPE_YDWZ,
            // 观看视频
            self::ACT_TYPE_GKSP
        ))) {
            $isTaskBehavior = true;
        }
        return $isTaskBehavior;
    }

    protected function addOrReducePoint(
        $member_id,
        $mobile,
        $openid,
        $name,
        $headimgurl,
        $pointRuleInfo,
        $now,
        $uniqueId,
        $activity_id,
        $channel,
        $points = null,
        array $memo = array('memo' => '')
    ) {
        $point_category = $pointRuleInfo['category'];
        // 备注
        $memo2 = array('pointRuleInfo' => $pointRuleInfo);
        if (empty($memo)) {
            $memo = array();
        }
        $memo = array_merge($memo, $memo2);

        $point_rule_id = $pointRuleInfo['id'];
        $point_rule_code = $pointRuleInfo['code'];
        if (is_null($points)) {
            $points = $pointRuleInfo['points'];
        }
        $pointUserInfo = $this->servicePoint->addOrReducePoint($point_category, $mobile, $name, $headimgurl, $points, $now, $uniqueId, $point_rule_id, $point_rule_code, $activity_id, $channel, $memo);
        if (!empty($pointUserInfo)) {
            // 获取徽章数量
            $pointUserInfo['got_points'] = $points;
        }
        return $pointUserInfo;
    }

    public static function getActTypeOptions()
    {
        $actTypeOptions = array();
        $actTypeOptions[strval(self::ACT_TYPE_BM)] = '报名';
        $actTypeOptions[strval(self::ACT_TYPE_YJ)] = '有介';
        $actTypeOptions[strval(self::ACT_TYPE_XYD)] = '心愿单';
        $actTypeOptions[strval(self::ACT_TYPE_SC)] = '收藏';
        $actTypeOptions[strval(self::ACT_TYPE_CJ)] = '成就';
        $actTypeOptions[strval(self::ACT_TYPE_DH)] = '兑换';
        $actTypeOptions[strval(self::ACT_TYPE_QD)] = '签到';
        $actTypeOptions[strval(self::ACT_TYPE_YDWZ)] = '阅读文章';
        $actTypeOptions[strval(self::ACT_TYPE_GKSP)] = '观看视频';
        $actTypeOptions[strval(self::ACT_TYPE_LOOK)] = '浏览产品';
        $actTypeOptions[strval(self::ACT_TYPE_INVITOR)] = '邀请好友报名';
        $actTypeOptions[strval(self::ACT_TYPE_CUSTOMER)] = '我的客户';
        return $actTypeOptions;
    }
}

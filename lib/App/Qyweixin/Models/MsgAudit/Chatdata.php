<?php

namespace App\Qyweixin\Models\MsgAudit;

class Chatdata extends \App\Common\Models\Qyweixin\MsgAudit\Chatdata
{
    /**
     *  根据msgid获取信息
     *
     * @param string $msgid    
     * @param string $mixed_item_idx         
     * @param string $agentid         
     * @param string $authorizer_appid            
     * @param string $provider_appid            
     */
    public function getInfoByMsgid($msgid, $mixed_item_idx, $agentid, $authorizer_appid, $provider_appid)
    {
        $query = array(
            'msgid' => $msgid,
            'mixed_item_idx' => $mixed_item_idx,
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'provider_appid' => $provider_appid
        );
        $info = $this->findOne($query);
        return $info;
    }

    public function saveChatdataInfo($chatdataInfo, $agentid, $authorizer_appid, $provider_appid)
    {
        $msgid = $chatdataInfo['msgid'];
        $mixed_item_idx = $chatdataInfo['mixed_item_idx'];
        $checkInfo = $this->getInfoByMsgid($msgid, $mixed_item_idx, $agentid, $authorizer_appid, $provider_appid);
        $data = $this->getPrepareData($chatdataInfo, $agentid, $authorizer_appid, $provider_appid, $checkInfo);
        // print_r($data);
        // die('saveChatdataInfo');
        if (!empty($checkInfo)) {
            $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
            return $chatdataInfo;
        } else {
            $checkInfo = $this->insert($data);
        }
        return $chatdataInfo;
    }

    private function getPrepareData($chatdataInfo, $agentid, $authorizer_appid, $provider_appid, $checkInfo)
    {
        $data = array();
        $msgtype = $data['msgtype'] = (isset($chatdataInfo['msgtype']) ? $chatdataInfo['msgtype'] : '');
        $mixed_item_msgtype = $data['mixed_item_msgtype'] = (isset($chatdataInfo['mixed_item_msgtype']) ? $chatdataInfo['mixed_item_msgtype'] : '');
        $msgtype = $this->checkAndGetMsgType($msgtype, $mixed_item_msgtype);

        if (empty($checkInfo)) {
            $data['agentid'] = $agentid;
            $data['authorizer_appid'] = $authorizer_appid;
            $data['provider_appid'] = $provider_appid;

            $data['seq'] = isset($chatdataInfo['seq']) ? $chatdataInfo['seq'] : '0';
            $data['publickey_ver'] = isset($chatdataInfo['publickey_ver']) ? $chatdataInfo['publickey_ver'] : '0';
            $data['encrypt_random_key'] = isset($chatdataInfo['encrypt_random_key']) ? $chatdataInfo['encrypt_random_key'] : '';
            $data['encrypt_chat_msg'] = isset($chatdataInfo['encrypt_chat_msg']) ? $chatdataInfo['encrypt_chat_msg'] : '';
            $data['decrypt_key'] = isset($chatdataInfo['decrypt_key']) ? $chatdataInfo['decrypt_key'] : '';
            $data['decrypt_chat_msg'] = isset($chatdataInfo['decrypt_chat_msg']) ? (is_array($chatdataInfo['decrypt_chat_msg']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['decrypt_chat_msg']) : $chatdataInfo['decrypt_chat_msg']) : '';

            $data['msgid'] = isset($chatdataInfo['msgid']) ? $chatdataInfo['msgid'] : '';
            $data['mixed_item_idx'] = isset($chatdataInfo['mixed_item_idx']) ? $chatdataInfo['mixed_item_idx'] : '0';
            $data['action'] = isset($chatdataInfo['action']) ? $chatdataInfo['action'] : '';
            $data['from'] = isset($chatdataInfo['from']) ? $chatdataInfo['from'] : '';
            $data['tolist'] = isset($chatdataInfo['tolist']) ? (is_array($chatdataInfo['tolist']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['tolist']) : $chatdataInfo['tolist']) : '';
            $data['touser'] = empty($data['tolist']) ? '' : \json_decode($data['tolist'], true)[0];
            $data['roomid'] = isset($chatdataInfo['roomid']) ? $chatdataInfo['roomid'] : '';
            $data['msgtime_orginal'] = isset($chatdataInfo['msgtime']) ? $chatdataInfo['msgtime'] : '';
            if (!empty($data['msgtime_orginal'])) {
                $data['msgtime'] = \App\Common\Utils\Helper::getCurrentTime($data['msgtime_orginal'] / 1000);
            }

            #文本
            if ($msgtype == 'text') {
                $data['text_content'] = isset($chatdataInfo['content']) ? $chatdataInfo['content'] : '';
            }
            #图片
            $data['sdkfileid'] = isset($chatdataInfo['sdkfileid']) ? $chatdataInfo['sdkfileid'] : '';
            $data['media_file'] = isset($chatdataInfo['media_file']) ? $chatdataInfo['media_file'] : '';
            $data['md5sum'] = isset($chatdataInfo['md5sum']) ? $chatdataInfo['md5sum'] : '';
            $data['filesize'] = isset($chatdataInfo['filesize']) ? $chatdataInfo['filesize'] : '0';
            #撤回消息
            if ($msgtype == 'revoke') {
                $data['revoke_pre_msgid'] = isset($chatdataInfo['pre_msgid']) ? $chatdataInfo['pre_msgid'] : '';
            }
            #同意会话聊天内容
            if ($msgtype == 'agree' || $msgtype == 'disagree') {
                $data['agree_userid'] = isset($chatdataInfo['userid']) ? $chatdataInfo['userid'] : '';
                $data['agree_agree_time_orginal'] = isset($chatdataInfo['agree_time']) ? $chatdataInfo['agree_time'] : '';
                if (!empty($data['agree_agree_time_orginal'])) {
                    $data['agree_agree_time'] = \App\Common\Utils\Helper::getCurrentTime($data['agree_agree_time_orginal'] / 1000);
                }
            }
            #语音
            if ($msgtype == 'voice') {
                $data['voice_size'] = isset($chatdataInfo['size']) ? $chatdataInfo['size'] : '0';
                $data['voice_play_length'] = isset($chatdataInfo['play_length']) ? $chatdataInfo['play_length'] : '0';
            }
            #视频
            if ($msgtype == 'video') {
                $data['video_play_length'] = isset($chatdataInfo['play_length']) ? $chatdataInfo['play_length'] : '0';
            }
            #名片
            if ($msgtype == 'card') {
                $data['card_corpname'] = isset($chatdataInfo['corpname']) ? $chatdataInfo['corpname'] : '';
                $data['card_userid'] = isset($chatdataInfo['userid']) ? $chatdataInfo['userid'] : '';
            }

            #位置
            if ($msgtype == 'location') {
                $data['location_longitude'] = isset($chatdataInfo['longitude']) ? $chatdataInfo['longitude'] : '0.000000';
                $data['location_latitude'] = isset($chatdataInfo['latitude']) ? $chatdataInfo['latitude'] : '0.000000';
                $data['location_address'] = isset($chatdataInfo['address']) ? $chatdataInfo['address'] : '';
                $data['location_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                $data['location_zoom'] = isset($chatdataInfo['zoom']) ? $chatdataInfo['zoom'] : '0';
            }
            #表情
            if ($msgtype == 'emotion') {
                $data['emotion_type'] = isset($chatdataInfo['type']) ? $chatdataInfo['type'] : '0';
                $data['emotion_width'] = isset($chatdataInfo['width']) ? $chatdataInfo['width'] : '0';
                $data['emotion_height'] = isset($chatdataInfo['height']) ? $chatdataInfo['height'] : '0';
                $data['emotion_imagesize'] = isset($chatdataInfo['imagesize']) ? $chatdataInfo['imagesize'] : '0';
            }

            #文件
            if ($msgtype == 'file') {
                $data['file_filename'] = isset($chatdataInfo['filename']) ? $chatdataInfo['filename'] : '';
                $data['file_fileext'] = isset($chatdataInfo['fileext']) ? $chatdataInfo['fileext'] : '';
            }
            #链接
            if ($msgtype == 'link') {
                $data['link_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                $data['link_description'] = isset($chatdataInfo['description']) ? $chatdataInfo['description'] : '';
                $data['link_link_url'] = isset($chatdataInfo['link_url']) ? $chatdataInfo['link_url'] : '';
                $data['link_image_url'] = isset($chatdataInfo['image_url']) ? $chatdataInfo['image_url'] : '';
            }
            #小程序
            if ($msgtype == 'weapp') {
                $data['weapp_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                $data['weapp_description'] = isset($chatdataInfo['description']) ? $chatdataInfo['description'] : '';
                $data['weapp_username'] = isset($chatdataInfo['username']) ? $chatdataInfo['username'] : '';
                $data['weapp_displayname'] = isset($chatdataInfo['displayname']) ? $chatdataInfo['displayname'] : '';
            }
            #会话记录消息
            if ($msgtype == 'chatrecord') {
                $data['chatrecord_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                $data['chatrecord_item'] = isset($chatdataInfo['item']) ? (is_array($chatdataInfo['item']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['item']) : $chatdataInfo['item']) : '';
            }

            #会话记录消息item
            if ($msgtype == 'item') {
                $data['item_type'] = isset($chatdataInfo['type']) ? $chatdataInfo['type'] : '';
                $data['item_content'] = isset($chatdataInfo['content']) ? (is_array($chatdataInfo['content']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['content']) : $chatdataInfo['content']) : '';
                $data['item_from_chatroom'] = isset($chatdataInfo['from_chatroom']) ? $chatdataInfo['from_chatroom'] : '0';
            }
            #待办消息
            if ($msgtype == 'todo') {
                $data['todo_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                $data['todo_content'] = isset($chatdataInfo['content']) ? $chatdataInfo['content'] : '';
            }

            #投票消息
            if ($msgtype == 'vote') {
                $data['vote_votetitle'] = isset($chatdataInfo['votetitle']) ? $chatdataInfo['votetitle'] : '';
                $data['vote_voteitem'] = isset($chatdataInfo['voteitem']) ? (is_array($chatdataInfo['voteitem']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['voteitem']) : $chatdataInfo['voteitem']) : '';
                $data['vote_votetype'] = isset($chatdataInfo['votetype']) ? $chatdataInfo['votetype'] : '0';
                $data['vote_voteid'] = isset($chatdataInfo['voteid']) ? $chatdataInfo['voteid'] : '';
            }

            #填表消息
            if ($msgtype == 'collect') {
                $data['collect_room_name'] = isset($chatdataInfo['room_name']) ? $chatdataInfo['room_name'] : '';
                $data['collect_creator'] = isset($chatdataInfo['creator']) ? $chatdataInfo['creator'] : '';
                $data['collect_create_time_orginal'] = isset($chatdataInfo['create_time']) ? $chatdataInfo['create_time'] : '';
                if (!empty($data['collect_create_time_orginal'])) {
                    $data['collect_create_time'] = $data['collect_create_time_orginal'];
                }
                $data['collect_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                $data['collect_details'] = isset($chatdataInfo['details']) ? (is_array($chatdataInfo['details']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['details']) : $chatdataInfo['details']) : '';
                $data['collect_id'] = isset($chatdataInfo['id']) ? $chatdataInfo['id'] : '0';
                $data['collect_ques'] = isset($chatdataInfo['ques']) ? $chatdataInfo['ques'] : '';
                $data['collect_type'] = isset($chatdataInfo['type']) ? $chatdataInfo['type'] : '';
            }

            #红包消息
            if ($msgtype == 'redpacket') {
                $data['redpacket_type'] = isset($chatdataInfo['type']) ? $chatdataInfo['type'] : '0';
                $data['redpacket_wish'] = isset($chatdataInfo['wish']) ? $chatdataInfo['wish'] : '';
                $data['redpacket_totalcnt'] = isset($chatdataInfo['totalcnt']) ? $chatdataInfo['totalcnt'] : '0';
                $data['redpacket_totalamount'] = isset($chatdataInfo['totalamount']) ? $chatdataInfo['totalamount'] : '0';
            }

            #会议邀请消息
            if ($msgtype == 'meeting') {
                $data['meeting_topic'] = isset($chatdataInfo['topic']) ? $chatdataInfo['topic'] : '';
                $data['meeting_starttime_orginal'] = isset($chatdataInfo['starttime']) ? $chatdataInfo['starttime'] : '';
                if (!empty($data['meeting_starttime_orginal'])) {
                    $data['meeting_starttime'] = \App\Common\Utils\Helper::getCurrentTime($data['meeting_starttime_orginal']);
                }
                $data['meeting_endtime_orginal'] = isset($chatdataInfo['endtime']) ? $chatdataInfo['endtime'] : '';
                if (!empty($data['meeting_endtime_orginal'])) {
                    $data['meeting_endtime'] = \App\Common\Utils\Helper::getCurrentTime($data['meeting_endtime_orginal']);
                }

                $data['meeting_address'] = isset($chatdataInfo['address']) ? $chatdataInfo['address'] : '';
                $data['meeting_remarks'] = isset($chatdataInfo['remarks']) ? $chatdataInfo['remarks'] : '';
                $data['meeting_meetingtype'] = isset($chatdataInfo['meetingtype']) ? $chatdataInfo['meetingtype'] : '0';
                $data['meeting_meetingid'] = isset($chatdataInfo['meetingid']) ? $chatdataInfo['meetingid'] : '0';
                $data['meeting_status'] = isset($chatdataInfo['status']) ? $chatdataInfo['status'] : '0';
            }

            #切换企业日志{"msgid":"125289002219525886280","action":"switch","time":1554119421840,"user":"XuJinSheng"}
            if ($msgtype == 'switch') {
                $data['switch_time_orginal'] = isset($chatdataInfo['time']) ? $chatdataInfo['time'] : '';
                if (!empty($data['switch_time_orginal'])) {
                    $data['switch_time'] = \App\Common\Utils\Helper::getCurrentTime($data['switch_time_orginal'] / 1000);
                }
                $data['switch_user'] = isset($chatdataInfo['user']) ? $chatdataInfo['user'] : '';
            }

            #在线文档消息
            if ($msgtype == 'docmsg') {
                $data['docmsg_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                $data['docmsg_link_url'] = isset($chatdataInfo['link_url']) ? $chatdataInfo['link_url'] : '';
                $data['docmsg_creator'] = isset($chatdataInfo['creator']) ? $chatdataInfo['creator'] : '';
            }

            #MarkDown格式消息
            if ($msgtype == 'markdown') {
                $data['markdown_content'] = isset($chatdataInfo['content']) ? $chatdataInfo['content'] : '';
            }

            #图文消息
            if ($msgtype == 'news') {
                $data['news_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                $data['news_description'] = isset($chatdataInfo['description']) ? $chatdataInfo['description'] : '';
                $data['news_url'] = isset($chatdataInfo['url']) ? $chatdataInfo['url'] : '';
                $data['news_picurl'] = isset($chatdataInfo['picurl']) ? $chatdataInfo['picurl'] : '';
            }

            #日程消息
            if ($msgtype == 'calendar') {
                $data['calendar_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                $data['calendar_creatorname'] = isset($chatdataInfo['creatorname']) ? $chatdataInfo['creatorname'] : '';
                $data['calendar_attendeename'] = isset($chatdataInfo['attendeename']) ? (is_array($chatdataInfo['attendeename']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['attendeename']) : $chatdataInfo['attendeename']) : '';

                $data['calendar_starttime_orginal'] = isset($chatdataInfo['starttime']) ? $chatdataInfo['starttime'] : '';
                if (!empty($data['calendar_starttime_orginal'])) {
                    $data['calendar_starttime'] = \App\Common\Utils\Helper::getCurrentTime($data['calendar_starttime_orginal']);
                }
                $data['calendar_endtime_orginal'] = isset($chatdataInfo['endtime']) ? $chatdataInfo['endtime'] : '';
                if (!empty($data['calendar_endtime_orginal'])) {
                    $data['calendar_endtime'] = \App\Common\Utils\Helper::getCurrentTime($data['calendar_endtime_orginal']);
                }
                $data['calendar_remarks'] = isset($chatdataInfo['remarks']) ? $chatdataInfo['remarks'] : '';
            }

            #混合消息{"msgid":"DAQQluDa4QUY0On4kYSABAMgzPrShAE=","action":"send","from":"HeMiao","tolist":["HeChangTian","LiuZeYu"],"roomid":"wr_tZ2BwAAUwHpYMwy9cIWqnlU3Hzqfg","msgtime":1577414359072,"msgtype":"mixed","mixed":{"item":[{"type":"text","content":"{\"content\":\"你好[微笑]\\n\"}"},{"type":"image","content":"{\"md5sum\":\"368b6c18c82e6441bfd89b343e9d2429\",\"filesize\":13177,\"sdkfileid\":\"CtYBMzA2OTAyMDEwMjA0NjIzMDYwMDIwMTAwMDWwNDVmYWY4Y2Q3MDIwMzBmNTliMTAyMDQwYzljNTQ3NzAyMDQ1ZTA1NmFlMjA0MjQ2NjM0NjIzNjY2MzYzNTMyMmQzNzYxMzQ2NDJkMzQ2MjYxNjQyZDM4MzMzMzM4MmQ3MTYyMzczMTM4NjM2NDYxMzczMjY2MzkwMjAxMDAwMjAzMDIwMDEwMDQxMDM2OGI2YzE4YzgyZTY0NDFiZmQ4OWIyNDNlOWQyNDI4MDIwMTAyMDIwMTAwMDQwMBI4TkRkZk2UWTRPRGcxTVRneE5URTFNRGc1TVY4eE1UTTFOak0yTURVeFh6RTFOemMwTVRNek5EYz0aIDQzMTY5NDFlM2MxZDRmZjhhMjEwY2M0NDQzZGUXOTEy\"}"}]}}
            if ($data['msgtype'] == 'mixed') {
                $data['mixed_mixed'] = isset($chatdataInfo['mixed']) ? (is_array($chatdataInfo['mixed']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['mixed']) : $chatdataInfo['mixed']) : '';
            }

            #音频存档消息{"msgid":"17952229780246929345_1594197637","action":"send","from":"wo137MCgAAYW6pIiKKrDe5SlzEhSgwbA","tolist":["wo137MCgAAYW6pIiKKrDe5SlzEhSgwbA"],"msgtime":1594197581,"msgtype":"meeting_voice_call","voiceid":"grb8a4c48a3c094a70982c518d55e40557","meeting_voice_call":{"endtime":1594197635,"sdkfileid":"CpsBKjAqd0xhb2JWRUJldGtwcE5DVTB6UjRUalN6c09vTjVyRnF4YVJ5M24rZC9YcHF3cHRPVzRwUUlaMy9iTytFcnc0SlBkZDU1YjRNb0MzbTZtRnViOXV5WjUwZUIwKzhjbU9uRUlxZ3pyK2VXSVhUWVN2ejAyWFJaTldGSkRJVFl0aUhkcVdjbDJ1L2RPbjJsRlBOamJaVDNnPT0SOE5EZGZNVFk0T0RnMU16YzVNVGt5T1RJMk9GOHhNalk0TXpBeE9EZzJYekUxT1RReE9UYzJNemM9GiA3YTYyNzA3NTY4Nzc2MTY3NzQ2MTY0NzA2ZTc4NjQ2OQ==","demofiledata":[{"filename":"65eb1cdd3e7a3c1740ecd74220b6c627.docx","demooperator":"wo137MCgAAYW6pIiKKrDe5SlzEhSgwbA","starttime":1594197599,"endtime":1594197609}],"sharescreendata":[{"share":"wo137MCgAAYW6pIiKKrDe5SlzEhSgwbA","starttime":1594197624,"endtime":1594197624}]}}
            if ($msgtype == 'meeting_voice_call') {
                $data['meeting_voice_call_endtime_orginal'] = isset($chatdataInfo['endtime']) ? $chatdataInfo['endtime'] : '';
                if (!empty($data['meeting_voice_call_endtime_orginal'])) {
                    $data['meeting_voice_call_endtime'] = \App\Common\Utils\Helper::getCurrentTime($data['meeting_voice_call_endtime_orginal']);
                }
                $data['meeting_voice_call_voiceid'] = isset($chatdataInfo['voiceid']) ? $chatdataInfo['voiceid'] : '';
                $data['meeting_voice_call_demofiledata'] = isset($chatdataInfo['demofiledata']) ? (is_array($chatdataInfo['demofiledata']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['demofiledata']) : $chatdataInfo['demofiledata']) : '';
                $data['meeting_voice_call_sharescreendata'] = isset($chatdataInfo['sharescreendata']) ? (is_array($chatdataInfo['sharescreendata']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['sharescreendata']) : $chatdataInfo['sharescreendata']) : '';
            }

            #音频共享文档消息{"msgid":"16527954622422422847_1594199256","action":"send","from":"18002520162","tolist":["wo137MCgAAYW6pIiKKrDe5SlzEhSgwbA"],"msgtime":1594199235,"msgtype":"voip_doc_share","voipid":"gr2751c98b19300571f8afb3b74514bd32","voip_doc_share":{"filename":"欢迎使用微盘.pdf.pdf","md5sum":"ff893900f24e55e216e617a40e5c4648","filesize":4400654,"sdkfileid":"CpsBKjAqZUlLdWJMd2gvQ1JxMzd0ZjlpdW5mZzJOOE9JZm5kbndvRmRqdnBETjY0QlcvdGtHSFFTYm95dHM2VlllQXhkUUN5KzRmSy9KT3pudnA2aHhYZFlPemc2aVZ6YktzaVh3YkFPZHlqNnl2L2MvcGlqcVRjRTlhZEZsOGlGdHJpQ2RWSVNVUngrVFpuUmo3TGlPQ1BJemlRPT0SOE5EZGZNVFk0T0RnMU16YzVNVGt5T1RJMk9GODFNelUyTlRBd01qQmZNVFU1TkRFNU9USTFOZz09GiA3YTcwNmQ2Zjc5NjY3MDZjNjY2Zjc4NzI3NTZmN2E2YQ=="}}
            if ($msgtype == 'voip_doc_share') {
                $data['voip_doc_share_voipid'] = isset($chatdataInfo['voipid']) ? $chatdataInfo['voipid'] : '';                //
                $data['voip_doc_share_filename'] = isset($chatdataInfo['filename']) ? $chatdataInfo['filename'] : '';
            }
        } else {
            if (isset($chatdataInfo['seq'])) {
                $data['seq'] = isset($chatdataInfo['seq']) ? $chatdataInfo['seq'] : '0';
            }
            if (isset($chatdataInfo['publickey_ver'])) {
                $data['publickey_ver'] = isset($chatdataInfo['publickey_ver']) ? $chatdataInfo['publickey_ver'] : '0';
            }
            if (isset($chatdataInfo['encrypt_random_key'])) {
                $data['encrypt_random_key'] = isset($chatdataInfo['encrypt_random_key']) ? $chatdataInfo['encrypt_random_key'] : '';
            }
            if (isset($chatdataInfo['encrypt_chat_msg'])) {
                $data['encrypt_chat_msg'] = isset($chatdataInfo['encrypt_chat_msg']) ? $chatdataInfo['encrypt_chat_msg'] : '';
            }
            if (isset($chatdataInfo['decrypt_key'])) {
                $data['decrypt_key'] = isset($chatdataInfo['decrypt_key']) ? $chatdataInfo['decrypt_key'] : '';
            }
            if (isset($chatdataInfo['decrypt_chat_msg'])) {
                $data['decrypt_chat_msg'] = isset($chatdataInfo['decrypt_chat_msg']) ? (is_array($chatdataInfo['decrypt_chat_msg']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['decrypt_chat_msg']) : $chatdataInfo['decrypt_chat_msg']) : '';
            }
            if (isset($chatdataInfo['msgid'])) {
                $data['msgid'] = $chatdataInfo['msgid'];
            }
            if (isset($chatdataInfo['mixed_item_idx'])) {
                $data['mixed_item_idx'] = $chatdataInfo['mixed_item_idx'];
            }
            if (isset($chatdataInfo['action'])) {
                $data['action'] = $chatdataInfo['action'];
            }
            if (isset($chatdataInfo['from'])) {
                $data['from'] = $chatdataInfo['from'];
            }
            if (isset($chatdataInfo['tolist'])) {
                $data['tolist'] = isset($chatdataInfo['tolist']) ? (is_array($chatdataInfo['tolist']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['tolist']) : $chatdataInfo['tolist']) : '';
                $data['touser'] = empty($data['tolist']) ? '' : \json_decode($data['tolist'], true)[0];
            }
            if (isset($chatdataInfo['roomid'])) {
                $data['roomid'] = $chatdataInfo['roomid'];
            }
            if (isset($chatdataInfo['msgtime'])) {
                $data['msgtime_orginal'] = $chatdataInfo['msgtime'];
                $data['msgtime'] = \App\Common\Utils\Helper::getCurrentTime($data['msgtime_orginal'] / 1000);
            }

            #文本
            if ($msgtype == 'text') {
                if (isset($chatdataInfo['content'])) {
                    $data['text_content'] = $chatdataInfo['text']['content'];
                }
            }
            #图片
            if (isset($chatdataInfo['sdkfileid'])) {
                $data['sdkfileid'] = $chatdataInfo['sdkfileid'];
            }
            if (isset($chatdataInfo['media_file'])) {
                $data['media_file'] = $chatdataInfo['media_file'];
            }
            if (isset($chatdataInfo['md5sum'])) {
                $data['md5sum'] = $chatdataInfo['md5sum'];
            }
            if (isset($chatdataInfo['filesize'])) {
                $data['filesize'] = $chatdataInfo['filesize'];
            }
            #撤回消息
            if ($msgtype == 'revoke') {
                if (isset($chatdataInfo['pre_msgid'])) {
                    $data['revoke_pre_msgid'] = $chatdataInfo['pre_msgid'];
                }
            }
            #同意会话聊天内容
            if ($msgtype == 'agree' || $msgtype == 'disagree') {
                if (isset($chatdataInfo['userid'])) {
                    $data['agree_userid'] = $chatdataInfo['userid'];
                }
                if (isset($chatdataInfo['agree_time'])) {
                    $data['agree_agree_time_orginal'] = $chatdataInfo['agree_time'];
                    $data['agree_agree_time'] = \App\Common\Utils\Helper::getCurrentTime($data['agree_agree_time_orginal'] / 1000);
                }
            }
            #语音
            if ($msgtype == 'voice') {
                if (isset($chatdataInfo['size'])) {
                    $data['voice_size'] = $chatdataInfo['size'];
                }
                if (isset($chatdataInfo['play_length'])) {
                    $data['voice_play_length'] = $chatdataInfo['play_length'];
                }
            }
            #视频
            if ($msgtype == 'video') {
                if (isset($chatdataInfo['play_length'])) {
                    $data['video_play_length'] = $chatdataInfo['play_length'];
                }
            }
            #名片
            if ($msgtype == 'card') {
                if (isset($chatdataInfo['corpname'])) {
                    $data['card_corpname'] = isset($chatdataInfo['corpname']) ? $chatdataInfo['corpname'] : '';
                }
                if (isset($chatdataInfo['userid'])) {
                    $data['card_userid'] = isset($chatdataInfo['userid']) ? $chatdataInfo['userid'] : '';
                }
            }

            #位置
            if ($msgtype == 'location') {
                if (isset($chatdataInfo['longitude'])) {
                    $data['location_longitude'] = isset($chatdataInfo['longitude']) ? $chatdataInfo['longitude'] : '0.000000';
                }
                if (isset($chatdataInfo['latitude'])) {
                    $data['location_latitude'] = isset($chatdataInfo['latitude']) ? $chatdataInfo['latitude'] : '0.000000';
                }
                if (isset($chatdataInfo['address'])) {
                    $data['location_address'] = isset($chatdataInfo['address']) ? $chatdataInfo['address'] : '';
                }
                if (isset($chatdataInfo['title'])) {
                    $data['location_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                }
                if (isset($chatdataInfo['zoom'])) {
                    $data['location_zoom'] = isset($chatdataInfo['zoom']) ? $chatdataInfo['zoom'] : '0';
                }
            }
            #表情
            if ($msgtype == 'emotion') {
                if (isset($chatdataInfo['type'])) {
                    $data['emotion_type'] = isset($chatdataInfo['type']) ? $chatdataInfo['type'] : '0';
                }
                if (isset($chatdataInfo['width'])) {
                    $data['emotion_width'] = isset($chatdataInfo['width']) ? $chatdataInfo['width'] : '0';
                }
                if (isset($chatdataInfo['height'])) {
                    $data['emotion_height'] = isset($chatdataInfo['height']) ? $chatdataInfo['height'] : '0';
                }
                if (isset($chatdataInfo['imagesize'])) {
                    $data['emotion_imagesize'] = isset($chatdataInfo['imagesize']) ? $chatdataInfo['imagesize'] : '0';
                }
            }

            #文件
            if ($msgtype == 'file') {
                if (isset($chatdataInfo['filename'])) {
                    $data['file_filename'] = isset($chatdataInfo['filename']) ? $chatdataInfo['filename'] : '';
                }
                if (isset($chatdataInfo['fileext'])) {
                    $data['file_fileext'] = isset($chatdataInfo['fileext']) ? $chatdataInfo['fileext'] : '';
                }
            }
            #链接
            if ($msgtype == 'link') {
                if (isset($chatdataInfo['title'])) {
                    $data['link_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                }
                if (isset($chatdataInfo['description'])) {
                    $data['link_description'] = isset($chatdataInfo['description']) ? $chatdataInfo['description'] : '';
                }
                if (isset($chatdataInfo['link_url'])) {
                    $data['link_link_url'] = isset($chatdataInfo['link_url']) ? $chatdataInfo['link_url'] : '';
                }
                if (isset($chatdataInfo['image_url'])) {
                    $data['link_image_url'] = isset($chatdataInfo['image_url']) ? $chatdataInfo['image_url'] : '';
                }
            }
            #小程序
            if ($msgtype == 'weapp') {
                if (isset($chatdataInfo['title'])) {
                    $data['weapp_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                }
                if (isset($chatdataInfo['description'])) {
                    $data['weapp_description'] = isset($chatdataInfo['description']) ? $chatdataInfo['description'] : '';
                }
                if (isset($chatdataInfo['username'])) {
                    $data['weapp_username'] = isset($chatdataInfo['username']) ? $chatdataInfo['username'] : '';
                }
                if (isset($chatdataInfo['displayname'])) {
                    $data['weapp_displayname'] = isset($chatdataInfo['displayname']) ? $chatdataInfo['displayname'] : '';
                }
            }
            #会话记录消息
            if ($msgtype == 'chatrecord') {
                if (isset($chatdataInfo['title'])) {
                    $data['chatrecord_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                }
                if (isset($chatdataInfo['item'])) {
                    $data['chatrecord_item'] = isset($chatdataInfo['item']) ? (is_array($chatdataInfo['item']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['item']) : $chatdataInfo['item']) : '';
                }
            }

            #会话记录消息item
            if ($msgtype == 'item') {
                if (isset($chatdataInfo['type'])) {
                    $data['item_type'] = isset($chatdataInfo['type']) ? $chatdataInfo['type'] : '';
                }
                if (isset($chatdataInfo['content'])) {
                    $data['item_content'] = isset($chatdataInfo['content']) ? (is_array($chatdataInfo['content']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['content']) : $chatdataInfo['content']) : '';
                }
                if (isset($chatdataInfo['from_chatroom'])) {
                    $data['item_from_chatroom'] = isset($chatdataInfo['from_chatroom']) ? $chatdataInfo['from_chatroom'] : '0';
                }
            }
            #待办消息
            if ($msgtype == 'todo') {
                if (isset($chatdataInfo['title'])) {
                    $data['todo_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                }
                if (isset($chatdataInfo['content'])) {
                    $data['todo_content'] = isset($chatdataInfo['content']) ? $chatdataInfo['content'] : '';
                }
            }

            #投票消息
            if ($msgtype == 'vote') {
                if (isset($chatdataInfo['votetitle'])) {
                    $data['vote_votetitle'] = isset($chatdataInfo['votetitle']) ? $chatdataInfo['votetitle'] : '';
                }
                if (isset($chatdataInfo['voteitem'])) {
                    $data['vote_voteitem'] = isset($chatdataInfo['voteitem']) ? (is_array($chatdataInfo['voteitem']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['voteitem']) : $chatdataInfo['voteitem']) : '';
                }
                if (isset($chatdataInfo['votetype'])) {
                    $data['vote_votetype'] = isset($chatdataInfo['votetype']) ? $chatdataInfo['votetype'] : '0';
                }
                if (isset($chatdataInfo['voteid'])) {
                    $data['vote_voteid'] = isset($chatdataInfo['voteid']) ? $chatdataInfo['voteid'] : '';
                }
            }

            #填表消息
            if ($msgtype == 'collect') {
                if (isset($chatdataInfo['room_name'])) {
                    $data['collect_room_name'] = isset($chatdataInfo['room_name']) ? $chatdataInfo['room_name'] : '';
                }
                if (isset($chatdataInfo['creator'])) {
                    $data['collect_creator'] = isset($chatdataInfo['creator']) ? $chatdataInfo['creator'] : '';
                }
                if (isset($chatdataInfo['create_time'])) {
                    $data['collect_create_time_orginal'] = isset($chatdataInfo['create_time']) ? $chatdataInfo['create_time'] : '';
                    if (!empty($data['collect_create_time_orginal'])) {
                        $data['collect_create_time'] = $data['collect_create_time_orginal'];
                    }
                }
                if (isset($chatdataInfo['title'])) {
                    $data['collect_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                }
                if (isset($chatdataInfo['details'])) {
                    $data['collect_details'] = isset($chatdataInfo['details']) ? (is_array($chatdataInfo['details']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['details']) : $chatdataInfo['details']) : '';
                }
                if (isset($chatdataInfo['id'])) {
                    $data['collect_id'] = isset($chatdataInfo['id']) ? $chatdataInfo['id'] : '0';
                }
                if (isset($chatdataInfo['ques'])) {
                    $data['collect_ques'] = isset($chatdataInfo['ques']) ? $chatdataInfo['ques'] : '';
                }
                if (isset($chatdataInfo['type'])) {
                    $data['collect_type'] = isset($chatdataInfo['type']) ? $chatdataInfo['type'] : '';
                }
            }

            #红包消息
            if ($msgtype == 'redpacket') {
                if (isset($chatdataInfo['type'])) {
                    $data['redpacket_type'] = isset($chatdataInfo['type']) ? $chatdataInfo['type'] : '0';
                }
                if (isset($chatdataInfo['wish'])) {
                    $data['redpacket_wish'] = isset($chatdataInfo['wish']) ? $chatdataInfo['wish'] : '';
                }
                if (isset($chatdataInfo['totalcnt'])) {
                    $data['redpacket_totalcnt'] = isset($chatdataInfo['totalcnt']) ? $chatdataInfo['totalcnt'] : '0';
                }
                if (isset($chatdataInfo['totalamount'])) {
                    $data['redpacket_totalamount'] = isset($chatdataInfo['totalamount']) ? $chatdataInfo['totalamount'] : '0';
                }
            }

            #会议邀请消息
            if ($msgtype == 'meeting') {
                if (isset($chatdataInfo['topic'])) {
                    $data['meeting_topic'] = isset($chatdataInfo['topic']) ? $chatdataInfo['topic'] : '';
                }
                if (isset($chatdataInfo['starttime'])) {
                    $data['meeting_starttime_orginal'] = isset($chatdataInfo['starttime']) ? $chatdataInfo['starttime'] : '';
                    if (!empty($data['meeting_starttime_orginal'])) {
                        $data['meeting_starttime'] = \App\Common\Utils\Helper::getCurrentTime($data['meeting_starttime_orginal']);
                    }
                }
                if (isset($chatdataInfo['endtime'])) {
                    $data['meeting_endtime_orginal'] = isset($chatdataInfo['endtime']) ? $chatdataInfo['endtime'] : '';
                    if (!empty($data['meeting_endtime_orginal'])) {
                        $data['meeting_endtime'] = \App\Common\Utils\Helper::getCurrentTime($data['meeting_endtime_orginal']);
                    }
                }
                if (isset($chatdataInfo['address'])) {
                    $data['meeting_address'] = isset($chatdataInfo['address']) ? $chatdataInfo['address'] : '';
                }
                if (isset($chatdataInfo['remarks'])) {
                    $data['meeting_remarks'] = isset($chatdataInfo['remarks']) ? $chatdataInfo['remarks'] : '';
                }
                if (isset($chatdataInfo['meetingtype'])) {
                    $data['meeting_meetingtype'] = isset($chatdataInfo['meetingtype']) ? $chatdataInfo['meetingtype'] : '0';
                }
                if (isset($chatdataInfo['meetingid'])) {
                    $data['meeting_meetingid'] = isset($chatdataInfo['meetingid']) ? $chatdataInfo['meetingid'] : '0';
                }
                if (isset($chatdataInfo['status'])) {
                    $data['meeting_status'] = isset($chatdataInfo['status']) ? $chatdataInfo['status'] : '0';
                }
            }

            #切换企业日志{"msgid":"125289002219525886280","action":"switch","time":1554119421840,"user":"XuJinSheng"}
            if ($msgtype == 'switch') {
                if (isset($chatdataInfo['time'])) {
                    $data['switch_time_orginal'] = isset($chatdataInfo['time']) ? $chatdataInfo['time'] : '';
                    if (!empty($data['switch_time_orginal'])) {
                        $data['switch_time'] = \App\Common\Utils\Helper::getCurrentTime($data['switch_time_orginal'] / 1000);
                    }
                }
                if (isset($chatdataInfo['user'])) {
                    $data['switch_user'] = isset($chatdataInfo['user']) ? $chatdataInfo['user'] : '';
                }
            }

            #在线文档消息
            if ($msgtype == 'docmsg') {
                if (isset($chatdataInfo['title'])) {
                    $data['docmsg_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                }
                if (isset($chatdataInfo['link_url'])) {
                    $data['docmsg_link_url'] = isset($chatdataInfo['link_url']) ? $chatdataInfo['link_url'] : '';
                }
                if (isset($chatdataInfo['creator'])) {
                    $data['docmsg_creator'] = isset($chatdataInfo['creator']) ? $chatdataInfo['creator'] : '';
                }
            }

            #MarkDown格式消息
            if ($msgtype == 'markdown') {
                if (isset($chatdataInfo['content'])) {
                    $data['markdown_content'] = isset($chatdataInfo['content']) ? $chatdataInfo['content'] : '';
                }
            }

            #图文消息
            if ($msgtype == 'news') {
                if (isset($chatdataInfo['title'])) {
                    $data['news_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                }
                if (isset($chatdataInfo['description'])) {
                    $data['news_description'] = isset($chatdataInfo['description']) ? $chatdataInfo['description'] : '';
                }
                if (isset($chatdataInfo['url'])) {
                    $data['news_url'] = isset($chatdataInfo['url']) ? $chatdataInfo['url'] : '';
                }
                if (isset($chatdataInfo['picurl'])) {
                    $data['news_picurl'] = isset($chatdataInfo['picurl']) ? $chatdataInfo['picurl'] : '';
                }
            }

            #日程消息
            if ($msgtype == 'calendar') {
                if (isset($chatdataInfo['title'])) {
                    $data['calendar_title'] = isset($chatdataInfo['title']) ? $chatdataInfo['title'] : '';
                }
                if (isset($chatdataInfo['creatorname'])) {
                    $data['calendar_creatorname'] = isset($chatdataInfo['creatorname']) ? $chatdataInfo['creatorname'] : '';
                }
                if (isset($chatdataInfo['attendeename'])) {
                    $data['calendar_attendeename'] = isset($chatdataInfo['attendeename']) ? (is_array($chatdataInfo['attendeename']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['attendeename']) : $chatdataInfo['attendeename']) : '';
                }
                if (isset($chatdataInfo['starttime'])) {
                    $data['calendar_starttime_orginal'] = isset($chatdataInfo['starttime']) ? $chatdataInfo['starttime'] : '';
                    if (!empty($data['calendar_starttime_orginal'])) {
                        $data['calendar_starttime'] = \App\Common\Utils\Helper::getCurrentTime($data['calendar_starttime_orginal']);
                    }
                }
                if (isset($chatdataInfo['endtime'])) {
                    $data['calendar_endtime_orginal'] = isset($chatdataInfo['endtime']) ? $chatdataInfo['endtime'] : '';
                    if (!empty($data['calendar_endtime_orginal'])) {
                        $data['calendar_endtime'] = \App\Common\Utils\Helper::getCurrentTime($data['calendar_endtime_orginal']);
                    }
                }
                if (isset($chatdataInfo['remarks'])) {
                    $data['calendar_remarks'] = isset($chatdataInfo['remarks']) ? $chatdataInfo['remarks'] : '';
                }
            }

            #混合消息{"msgid":"DAQQluDa4QUY0On4kYSABAMgzPrShAE=","action":"send","from":"HeMiao","tolist":["HeChangTian","LiuZeYu"],"roomid":"wr_tZ2BwAAUwHpYMwy9cIWqnlU3Hzqfg","msgtime":1577414359072,"msgtype":"mixed","mixed":{"item":[{"type":"text","content":"{\"content\":\"你好[微笑]\\n\"}"},{"type":"image","content":"{\"md5sum\":\"368b6c18c82e6441bfd89b343e9d2429\",\"filesize\":13177,\"sdkfileid\":\"CtYBMzA2OTAyMDEwMjA0NjIzMDYwMDIwMTAwMDWwNDVmYWY4Y2Q3MDIwMzBmNTliMTAyMDQwYzljNTQ3NzAyMDQ1ZTA1NmFlMjA0MjQ2NjM0NjIzNjY2MzYzNTMyMmQzNzYxMzQ2NDJkMzQ2MjYxNjQyZDM4MzMzMzM4MmQ3MTYyMzczMTM4NjM2NDYxMzczMjY2MzkwMjAxMDAwMjAzMDIwMDEwMDQxMDM2OGI2YzE4YzgyZTY0NDFiZmQ4OWIyNDNlOWQyNDI4MDIwMTAyMDIwMTAwMDQwMBI4TkRkZk2UWTRPRGcxTVRneE5URTFNRGc1TVY4eE1UTTFOak0yTURVeFh6RTFOemMwTVRNek5EYz0aIDQzMTY5NDFlM2MxZDRmZjhhMjEwY2M0NDQzZGUXOTEy\"}"}]}}
            if ($data['msgtype'] == 'mixed') {
                if (isset($chatdataInfo['mixed'])) {
                    $data['mixed_mixed'] = isset($chatdataInfo['mixed']) ? (is_array($chatdataInfo['mixed']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['mixed']) : $chatdataInfo['mixed']) : '';
                }
            }

            #音频存档消息{"msgid":"17952229780246929345_1594197637","action":"send","from":"wo137MCgAAYW6pIiKKrDe5SlzEhSgwbA","tolist":["wo137MCgAAYW6pIiKKrDe5SlzEhSgwbA"],"msgtime":1594197581,"msgtype":"meeting_voice_call","voiceid":"grb8a4c48a3c094a70982c518d55e40557","meeting_voice_call":{"endtime":1594197635,"sdkfileid":"CpsBKjAqd0xhb2JWRUJldGtwcE5DVTB6UjRUalN6c09vTjVyRnF4YVJ5M24rZC9YcHF3cHRPVzRwUUlaMy9iTytFcnc0SlBkZDU1YjRNb0MzbTZtRnViOXV5WjUwZUIwKzhjbU9uRUlxZ3pyK2VXSVhUWVN2ejAyWFJaTldGSkRJVFl0aUhkcVdjbDJ1L2RPbjJsRlBOamJaVDNnPT0SOE5EZGZNVFk0T0RnMU16YzVNVGt5T1RJMk9GOHhNalk0TXpBeE9EZzJYekUxT1RReE9UYzJNemM9GiA3YTYyNzA3NTY4Nzc2MTY3NzQ2MTY0NzA2ZTc4NjQ2OQ==","demofiledata":[{"filename":"65eb1cdd3e7a3c1740ecd74220b6c627.docx","demooperator":"wo137MCgAAYW6pIiKKrDe5SlzEhSgwbA","starttime":1594197599,"endtime":1594197609}],"sharescreendata":[{"share":"wo137MCgAAYW6pIiKKrDe5SlzEhSgwbA","starttime":1594197624,"endtime":1594197624}]}}
            if ($msgtype == 'meeting_voice_call') {
                if (isset($chatdataInfo['voiceid'])) {
                    $data['meeting_voice_call_voiceid'] = isset($chatdataInfo['voiceid']) ? $chatdataInfo['voiceid'] : '';
                }
                if (isset($chatdataInfo['endtime'])) {
                    $data['meeting_voice_call_endtime_orginal'] = isset($chatdataInfo['endtime']) ? $chatdataInfo['endtime'] : '';
                    if (!empty($data['meeting_voice_call_endtime_orginal'])) {
                        $data['meeting_voice_call_endtime'] = \App\Common\Utils\Helper::getCurrentTime($data['meeting_voice_call_endtime_orginal']);
                    }
                }

                if (isset($chatdataInfo['demofiledata'])) {
                    $data['meeting_voice_call_demofiledata'] = isset($chatdataInfo['demofiledata']) ? (is_array($chatdataInfo['demofiledata']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['demofiledata']) : $chatdataInfo['demofiledata']) : '';
                }
                if (isset($chatdataInfo['sharescreendata'])) {
                    $data['meeting_voice_call_sharescreendata'] = isset($chatdataInfo['sharescreendata']) ? (is_array($chatdataInfo['sharescreendata']) ? \App\Common\Utils\Helper::myJsonEncode($chatdataInfo['sharescreendata']) : $chatdataInfo['sharescreendata']) : '';
                }
            }

            #音频共享文档消息{"msgid":"16527954622422422847_1594199256","action":"send","from":"18002520162","tolist":["wo137MCgAAYW6pIiKKrDe5SlzEhSgwbA"],"msgtime":1594199235,"msgtype":"voip_doc_share","voipid":"gr2751c98b19300571f8afb3b74514bd32","voip_doc_share":{"filename":"欢迎使用微盘.pdf.pdf","md5sum":"ff893900f24e55e216e617a40e5c4648","filesize":4400654,"sdkfileid":"CpsBKjAqZUlLdWJMd2gvQ1JxMzd0ZjlpdW5mZzJOOE9JZm5kbndvRmRqdnBETjY0QlcvdGtHSFFTYm95dHM2VlllQXhkUUN5KzRmSy9KT3pudnA2aHhYZFlPemc2aVZ6YktzaVh3YkFPZHlqNnl2L2MvcGlqcVRjRTlhZEZsOGlGdHJpQ2RWSVNVUngrVFpuUmo3TGlPQ1BJemlRPT0SOE5EZGZNVFk0T0RnMU16YzVNVGt5T1RJMk9GODFNelUyTlRBd01qQmZNVFU1TkRFNU9USTFOZz09GiA3YTcwNmQ2Zjc5NjY3MDZjNjY2Zjc4NzI3NTZmN2E2YQ=="}}
            if ($msgtype == 'voip_doc_share') {
                if (isset($chatdataInfo['voipid'])) {
                    $data['voip_doc_share_voipid'] = isset($chatdataInfo['voipid']) ? $chatdataInfo['voipid'] : '';
                }
                if (isset($chatdataInfo['filename'])) {
                    $data['voip_doc_share_filename'] = isset($chatdataInfo['filename']) ? $chatdataInfo['filename'] : '';
                }
            }
        }
        return $data;
    }

    protected function checkAndGetMsgType($msgtype, $mixed_item_msgtype)
    {
        // 检查msgtype是否有值
        if (empty($msgtype)) {
            return $msgtype;
        }
        // 如果是混合消息的话
        if ($msgtype == 'mixed') {
            // 检查混合消息包含的消息的type是否有值
            if (empty($mixed_item_msgtype)) {
                throw new \Exception("mixed_item_msgtype is empty");
            }
            return  $mixed_item_msgtype;
        } else {
            return $msgtype;
        }
    }
}

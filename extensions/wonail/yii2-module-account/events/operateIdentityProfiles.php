<?php

namespace wocenter\backend\modules\account\events;

use wocenter\backend\modules\account\models\Identity;
use wocenter\backend\modules\account\models\IdentityProfile;
use Yii;
use yii\db\AfterSaveEvent;

/**
 * 操作身份 - 档案关联数据
 *
 * @auth E-Kevin <e-kevin@qq.com>
 */
class operateIdentityProfiles
{
    
    /**
     * 添加身份 - 档案关联数据
     *
     * @param AfterSaveEvent $event
     */
    public function create(AfterSaveEvent $event)
    {
        /** @var Identity $sender */
        $sender = $event->sender;
        // 已选档案ID
        $profileId = $sender->profileId;
        if (!empty($profileId)) {
            $arr = [];
            foreach ($profileId as $profile_id) {
                $arr[] = [
                    'profile_id' => $profile_id,
                    'identity_id' => $sender->id,
                ];
            }
            Yii::$app->getDb()->createCommand()->batchInsert(IdentityProfile::tableName(), array_keys($arr[0]), $arr)->execute();
        }
    }
    
    /**
     * 更新身份 - 档案关联数据
     *
     * @param AfterSaveEvent $event
     */
    public function update(AfterSaveEvent $event)
    {
        /** @var Identity $sender */
        $sender = $event->sender;
        // 已选档案ID
        $profileId = $sender->profileId;
        if (!empty($profileId)) {
            // 原有数据
            $oldProfileIds = $sender->getIdentityProfiles()->select('profile_id')->column();
            $add = array_diff($profileId, $oldProfileIds);
            if (!empty($add)) {
                $arr = [];
                foreach ($add as $profile_id) {
                    $arr[] = [
                        'profile_id' => $profile_id,
                        'identity_id' => $sender->id,
                    ];
                }
                Yii::$app->getDb()->createCommand()->batchInsert(IdentityProfile::tableName(), array_keys($arr[0]), $arr)->execute();
            }
            
            $del = array_diff($oldProfileIds, $profileId);
            if (!empty($del)) {
                IdentityProfile::deleteAll(['identity_id' => $sender->id, 'profile_id' => $del]);
            }
        } else {
            IdentityProfile::deleteAll(['identity_id' => $sender->id]);
        }
    }
    
}

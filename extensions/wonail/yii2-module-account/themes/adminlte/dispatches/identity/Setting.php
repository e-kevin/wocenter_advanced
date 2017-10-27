<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\identity;

use wocenter\backend\modules\account\models\Identity;
use wocenter\backend\modules\account\models\IdentityConfig;
use wocenter\backend\modules\account\models\SettingProfileForm;
use wocenter\backend\modules\account\models\SettingRankForm;
use wocenter\backend\modules\account\models\SettingScoreForm;
use wocenter\backend\modules\account\models\SettingSignupForm;
use wocenter\backend\modules\account\models\SettingTagForm;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\traits\LoadModelTrait;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class Setting
 */
class Setting extends Dispatch
{
    
    use LoadModelTrait;
    
    /**
     * @var IdentityConfig 身份配置模型数据
     */
    private $_configModel;
    
    /**
     * @var Identity 身份模型
     */
    private $_identityModel;
    
    /**
     * @param string $type 配置类型 [score, avatar, rank, tag, profile, signup]
     * @param integer $id 身份ID
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function run($type, $id)
    {
        // 身份是否存在
        $this->_identityModel = $this->loadModel(Identity::className(), $id);
        // 获取身份配置数据
        $this->_configModel = $this->_identityModel->getIdentityConfigs()->where(['name' => $type])->one();
        if ($this->_configModel) {
            $this->_configModel->parseConfig($this->_configModel);
        } else {
            $this->_configModel = new IdentityConfig([
                'identity_id' => $id,
                'name' => $type,
            ]);
        }
        
        // 身份筛选列表
        $this->assign('identityList', $this->_identityModel->getSelectList());
        
        switch ($type) {
            case 'score':
                return $this->_scoreSetting();
            case 'avatar':
                return $this->_avatarSetting();
            case 'rank':
                return $this->_rankSetting();
            case 'tag':
                return $this->_tagSetting();
            case 'profile':
                return $this->_profileSetting();
            case 'signup':
                return $this->_signupSetting();
            default:
                throw new NotFoundHttpException(Yii::t('wocenter/app', 'Page not found.'));
        }
    }
    
    /**
     * 奖励设置
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    private function _scoreSetting()
    {
        $model = new SettingScoreForm([
            'configModel' => $this->_configModel,
        ]);
        
        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()->getBodyParams()) && $model->save()) {
                $this->success($model->message);
            } else {
                if (empty($model->message)) {
                    $this->success();
                } else {
                    $this->error($model->message);
                }
            }
        }
        
        return $this->display('setting-score', [
            'model' => $model,
            'scoreList' => $model->getScoreList(),
            'tips' => '获得该身份的用户默认可以获得以下的奖励',
        ]);
    }
    
    /**
     * 头衔设置
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    private function _rankSetting()
    {
        $model = new SettingRankForm([
            'configModel' => $this->_configModel,
            'rank' => ArrayHelper::getValue($this->_configModel, 'value.data'),
            'reason' => ArrayHelper::getValue($this->_configModel, 'value.reason'),
        ]);
        
        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()->getBodyParams()) && $model->save()) {
                $this->success($model->message);
            } else {
                if (empty($model->message)) {
                    $this->success();
                } else {
                    $this->error($model->message);
                }
            }
        }
        
        return $this->display('setting-rank', [
            'model' => $model,
            'rankList' => $model->getRankList(),
            'tips' => '获得该身份的用户默认可以获得以下的头衔',
        ]);
    }
    
    /**
     * 标签设置
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    private function _tagSetting()
    {
        $model = new SettingTagForm([
            'configModel' => $this->_configModel,
            'tag' => ArrayHelper::getValue($this->_configModel, 'value'),
        ]);
        
        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()->getBodyParams()) && $model->save()) {
                $this->success($model->message);
            } else {
                if (empty($model->message)) {
                    $this->success();
                } else {
                    $this->error($model->message);
                }
            }
        }
        
        return $this->display('setting-tag', [
            'model' => $model,
            'tagList' => $model->getTagList(),
            'tips' => '获得该身份的用户默认可以获得以下的标签',
        ]);
    }
    
    /**
     * 档案设置
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    private function _profileSetting()
    {
        $model = new SettingProfileForm([
            'configModel' => $this->_configModel,
            'identityModel' => $this->_identityModel,
            'fields' => ArrayHelper::getValue($this->_configModel, 'value'),
        ]);
        
        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()->getBodyParams()) && $model->save()) {
                $this->success($model->message);
            } else {
                if (empty($model->message)) {
                    $this->success();
                } else {
                    $this->error($model->message);
                }
            }
        }
        
        return $this->display('setting-profile', [
            'model' => $model,
            'fieldList' => $model->getFieldList(),
            'tips' => '获得该身份的用户默认可以拥有以下勾选的档案字段',
        ]);
    }
    
    /**
     * 注册设置
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    private function _signupSetting()
    {
        $model = new SettingSignupForm([
            'configModel' => $this->_configModel,
            'identityModel' => $this->_identityModel,
            'fields' => ArrayHelper::getValue($this->_configModel, 'value'),
        ]);
        
        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()->getBodyParams()) && $model->save()) {
                $this->success($model->message);
            } else {
                if (empty($model->message)) {
                    $this->success();
                } else {
                    $this->error($model->message);
                }
            }
        }
        
        return $this->display('setting-signup', [
            'model' => $model,
            'fieldList' => $model->getFieldList(),
            'tips' => '获得该身份的用户在注册时需要填写以下勾选的档案字段',
        ]);
    }
    
    /**
     * 头像设置
     *
     * @return string|\yii\web\Response
     */
    private function _avatarSetting()
    {
        return $this->display('setting-avatar');
    }
    
}

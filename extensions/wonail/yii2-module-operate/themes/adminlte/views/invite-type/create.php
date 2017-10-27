<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteType */
/* @var $scoreList array 积分列表 */
/* @var $inviteIdentityList array 可邀请注册的身份列表 */

$this->title = '新建邀请码类型';
$this->params['breadcrumbs'][] = ['label' => '邀请码类型', 'url' => ['/operate/invite-type/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/operate/invite-type/index';
?>

<?= $this->render('_form', [
    'model' => $model,
    'scoreList' => $scoreList,
    'inviteIdentityList' => $inviteIdentityList
]) ?>
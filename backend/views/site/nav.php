<?php
use rbac\components\MenuHelper;
use backend\widgets\Menu;
?>
<?php
$callback = function($menu){
    $items = $menu['children'];
    $return = [
        'label' => $menu['name'],
        'url' => [$menu['route']],
    ];
    if(isset($menu['icon'])){
        $return['icon'] = $menu['icon'];
    }else{
        $return['icon'] = 'fa fa-circle-o';
    }
    $items && $return['items'] = $items;
    return $return;
};
$menu = Menu::widget([
    'options' => ['class' => 'layui-nav layui-nav-tree my_define_auto_height'],
    'items' => MenuHelper::getAssignedMenu(Yii::$app->user->id, null, $callback),
]);
?>
<div class="layui-side layui-side-menu">
    <div class="layui-side-scroll">
        <div class="layui-logo" lay-href="home/console.html">
            <span>ERP供应链管理</span>
        </div>
        <?=$menu?>
    </div>
</div>

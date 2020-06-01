<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:69:"D:\phpStudy\PHPTutorial\WWW\aurora\addons\third\view\index\index.html";i:1586421500;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <title>第三方登录 - <?php echo $site['name']; ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="/assets/css/frontend.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://cdn.jsdelivr.net/npm/html5shiv@3.7.3/dist/html5shiv.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/respond.js@1.4.2/dest/respond.min.js"></script>
    <![endif]-->

</head>
<body>
<div class="container">
    <h2>第三方登录</h2>
    <hr>
    <div class="well">
        <div class="row">
            <div class="col-xs-4">
                <?php if($user && in_array('qq', $platformList)): ?>
                <a href="<?php echo addon_url('third/index/unbind',[':platform'=>'qq']); ?>" class="btn btn-block btn-info">
                    <i class="fa fa-qq"></i> 点击解绑
                </a>
                <?php else: ?>
                <a href="<?php echo addon_url('third/index/connect',[':platform'=>'qq']); ?>" class="btn btn-block btn-info">
                    <i class="fa fa-qq"></i> QQ登录
                </a>
                <?php endif; ?>
            </div>
            <div class="col-xs-4">
                <?php if($user && in_array('wechat', $platformList)): ?>
                <a href="<?php echo addon_url('third/index/unbind',[':platform'=>'wechat']); ?>" class="btn btn-block btn-success">
                    <i class="fa fa-wechat"></i> 点击解绑
                </a>
                <?php else: ?>
                <a href="<?php echo addon_url('third/index/connect',[':platform'=>'wechat']); ?>" class="btn btn-block btn-success">
                    <i class="fa fa-wechat"></i> 微信登录
                </a>
                <?php endif; ?>
            </div>
            <div class="col-xs-4">
                <?php if($user && in_array('weibo', $platformList)): ?>
                <a href="<?php echo addon_url('third/index/unbind',[':platform'=>'weibo']); ?>" class="btn btn-block btn-danger">
                    <i class="fa fa-weibo"></i> 点击解绑
                </a>
                <?php else: ?>
                <a href="<?php echo addon_url('third/index/connect',[':platform'=>'weibo']); ?>" class="btn btn-block btn-danger">
                    <i class="fa fa-weibo"></i> 微博登录
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <h2>相关链接</h2>
    <hr>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>QQ</th>
            <th>链接</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>QQ 连接</td>
            <td><?php echo addon_url('third/index/connect',[':platform'=>'qq'], false, true); ?></td>
        </tr>
        <tr>
            <td>QQ 绑定</td>
            <td><?php echo addon_url('third/index/bind',[':platform'=>'qq'], false, true); ?></td>
        </tr>
        <tr>
            <td>QQ 解绑</td>
            <td><?php echo addon_url('third/index/unbind',[':platform'=>'qq'], false, true); ?></td>
        </tr>
        </tbody>
    </table>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>微信</th>
            <th>链接</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>微信 连接</td>
            <td><?php echo addon_url('third/index/connect',[':platform'=>'wechat'], false, true); ?></td>
        </tr>
        <tr>
            <td>微信 绑定</td>
            <td><?php echo addon_url('third/index/bind',[':platform'=>'wechat'], false, true); ?></td>
        </tr>
        <tr>
            <td>微信 解绑</td>
            <td><?php echo addon_url('third/index/unbind',[':platform'=>'wechat'], false, true); ?></td>
        </tr>
        </tbody>
    </table>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>微博</th>
            <th>链接</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>微博 连接</td>
            <td><?php echo addon_url('third/index/connect',[':platform'=>'weibo'], false, true); ?></td>
        </tr>
        <tr>
            <td>微博 绑定</td>
            <td><?php echo addon_url('third/index/bind',[':platform'=>'weibo'], false, true); ?></td>
        </tr>
        <tr>
            <td>微博 解绑</td>
            <td><?php echo addon_url('third/index/unbind',[':platform'=>'weibo'], false, true); ?></td>
        </tr>
        </tbody>
    </table>
</div>
<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@2.1.4/dist/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>

<script type="text/javascript">
    $(function () {

    });
</script>
</body>
</html>
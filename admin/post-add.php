<?php 

require_once '../functions.php';
// 检查用户是否登陆
bx_get_current_user();

//查询数据
//====================================================================================================

//查询分类数据
$categories = bx_fetch_all('select * from categories');

// 处理数据提交请求
//====================================================================================================
  //数据校验
  //---------------------------------------
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(empty($_POST['title'])
    || empty($_POST['slug'])
    || empty($_POST['feature'])
    || empty($_POST['category'])
    || empty($_POST['created'])
    || empty($_POST['status'])
    || empty($_POST['content']) ){
      //缺少必要数据
      $message = '请填写完整内容';
    }else if(bx_fetch_one(sprintf("select count(1) as count from posts where slug = '%s'",$_POST['slug']))['count'] > 0){
      //slug重复
      $message = 'slug重复,请重新填写slug';
    }else{
      //数据合法
      //持久化
      //---------------------------------------
      // bx_execute()
    }
  }
      

// 2.持久化
// 3.跳转

;?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (!empty($message)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message; ?>
      </div>
      <?php endif ?>
      <form class="row" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题" value="<?php echo isset($_POST['title'])?$_POST['title']:''; ?>">
          </div>
          <div class="form-group">
            <label for="content">标题</label>
            <!-- <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"></textarea> -->
            <script id="content" name="content" type="text/plain"><?php echo isset($_POST['content'])?$_POST['content']:''; ?></script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo isset($_POST['slug'])?$_POST['slug']:''; ?>">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file" value="<?php echo isset($_POST['feature'])?$_POST['feature']:''; ?>">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($categories as $item): ?>
              <option value="<?php echo $item['id'] ;?>"<?php echo isset($_POST['category']) && $item['id'] == $_POST['category'] ? ' selected' : ''; ?>><?php echo $item['name']; ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local" value="<?php echo isset($_POST['created'])?$_POST['created']:''; ?>">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted"<?php echo isset($_POST['status']) && $_POST['status'] == 'drafted'?' selected':''; ?>>草稿</option>
              <option value="published"<?php echo isset($_POST['status']) && $_POST['status'] == 'published'?' selected':''; ?>>已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current_page = 'post-add'; ?>
  <?php include 'inc/sidebar.php'; ?>


  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.config.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.all.js"></script>
  <script>
    var ue = UE.getEditor('content',{
      initialFrameHeight: 520,
      autoHeight: false
    });
  </script>
  <script>NProgress.done()</script>
  <script>
    $('#feature').on('change', function () {
      var file = $(this).prop('files')[0];
      // 为这个文件对象创建一个 Object URL
      var url = URL.createObjectURL(file);
      // url => blob:http://zce.me/65a03a19‐3e3a‐446a‐9956‐e91cb2b76e1f
      // 将图片元素显示到界面上（预览）
      $(this).siblings('.thumbnail').attr('src', url).fadeIn();
        // .end().attr('style','display:none');
    })
  </script>
</body>
</html>

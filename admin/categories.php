<?php 

require_once '../functions.php';
// 检查用户是否登陆
bx_get_current_user();

// 添加分类
function add_categories() {
  if(empty($_POST['name']) || empty($_POST['slug'])){
    $GLOBALS['message'] = '请填写完整的信息';
    $GLOBALS['success'] = false;
    return;
  }

  // 接收并保存
  $name = $_POST['name'];
  $slug = $_POST['slug'];

  $rows = bx_execute("insert into categories values (null,'{$slug}','{$name}');");

  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ? '添加失败！' : '添加成功！';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  add_categories();
}

$categories = bx_fetch_all('select * from categories');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(!empty($message)): ?>
        <div class="alert<?php echo !$success?' alert-danger':' alert-success'; ?>">
          <strong><?php echo $success?'':'错误！'; ?></strong><?php echo $message; ?>
        </div>
      <?php endif ?>      
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn-delete" class="btn btn-danger btn-sm" href="/admin/category-delete.php?id=<?php echo $item['id']; ?>" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item): ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/category-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function($){

      var $checkBoxs = $('tbody input');
      var $btnDelete = $('#btn-delete');

      var allCheckeds = [];

      $checkBoxs.on('change',function(){
        
        var $id = $(this).data('id');

        if($(this).prop('checked')){
          allCheckeds.push($id);
        }else{
          allCheckeds.splice(allCheckeds.indexOf($id),1);
        }

        allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
        $btnDelete.prop('search','id=' + allCheckeds)

      })

    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>

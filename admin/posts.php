<?php 

require_once '../functions.php';
// 检查用户是否登陆
bx_get_current_user();

// 接收筛选参数
// ==================================

$where = '1 = 1';
$search = '';

// 分类筛选
if (isset($_GET['category']) && $_GET['category'] !== 'all') {
  $where .= ' and posts.category_id = ' . $_GET['category'];
  $search .= '&category=' . $_GET['category'];
}

if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  $where .= " and posts.status = '{$_GET['status']}'";
  $search .= '&status=' . $_GET['status'];
}

// $where => "1 = 1 and posts.category_id = 1 and posts.status = 'published'"
// $search => "&category=1&status=published"

// 处理分页参数
// =========================================

$size = 20;
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];

if($page < 1 ){
  header('location:/admin/posts.php?page=1' . $search);
}

// 查询数据总条数
$total_count = (int)bx_fetch_one("select count(1) as count from posts
  inner join categories on posts.category_id = categories.id
  inner join users on posts.user_id = users.id
  where {$where};
  ")['count'];

//总页数
$total_pages = (int)ceil($total_count / $size);

if ($page > $total_pages) {
  header('location:/admin/posts.php?page=' . $total_pages . $search);
}

//获取全部数据
//==========================================================

//计算越过多少条数据
$offset = ( $page - 1 ) * $size;

// 查询文章列表
$posts = bx_fetch_all("
  select 
    posts.id,
    posts.title,
    users.nickname as user_name,
    categories.`name` as category_name,
    posts.created,
    posts.status
  from posts
  inner join users on posts.user_id = users.id
  inner join categories on posts.category_id = categories.id
  where {$where}
  order by posts.created desc
  limit {$offset},{$size};
  ");

//查询分类数据
$categories = bx_fetch_all('select * from categories');

//处理分页页码
//==========================================================
$visiable = 5;

$begin = $page - ($visiable - 1) / 2;
$end = $begin + $visiable - 1;

//页码数的合理性
//$begin > 0 ;$end < $total_pages
if($begin < 1){
  $begin = 1;
  $end = $begin + $visiable - 1;
}
if($end
 > $total_pages){
  $end = $total_pages;
  $begin = $end - $visiable + 1;
}
$begin = $begin < 1 ? $begin = 1 : $begin;//在总条数小于visiable时begin = 1；

// 状态格式转换
function convert_status($status) {
  $dict = array(
    'published' => '已发布',
    'drafted' => '草稿',
    'trashed' => '回收站'
  );

  return isset($dict[$status]) ? $dict[$status] : '未知状态';
}

// 时间格式转换
function convert_date($created) {
  $timestamp = strtotime($created);
  return date('Y年m月d日<b\r>H:i:s',$timestamp);
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="/admin/post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/post-delete.php?id=" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $item): ?>
            <option value="<?php echo $item['id']; ?>"<?php echo isset($_GET['category']) && $_GET['category'] === $item['id'] ? ' selected':''; ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted"<?php echo isset($_GET['status']) && $_GET['status'] == 'drafted' ? ' selected' : '' ?>>草稿</option>
            <option value="published"<?php echo isset($_GET['status']) && $_GET['status'] == 'published' ? ' selected' : '' ?>>已发布</option>
            <option value="trashed"<?php echo isset($_GET['status']) && $_GET['status'] == 'trashed' ? ' selected' : '' ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
          <?php for($i = $begin; $i <= $end; $i++): ?>
          <li<?php echo $i === $page ? ' class="active"' : ''; ?>><a href="?page=<?php echo $i . $search;?>"><?php echo $i; ?></a></li>
          <?php endfor; ?>
          <li><a href="#">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
          <tr>
            <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
            <td><?php echo $item['title']; ?></td>
            <td><?php echo $item['user_name']; ?></td>
            <td><?php echo $item['category_name']; ?></td>
            <td class="text-center"><?php echo convert_date($item['created']); ?></td>
            <td class="text-center"><?php echo convert_status($item['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/post-delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <!--  侧边栏 -->
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>
    $(function($){
      //将选择的文章数据加入数组
      var $tbodyCheckboxs = $('tbody input');
      var $btnDelete = $('#btn_delete')
      var allCheckeds = [];

      $tbodyCheckboxs.on('change',function(){       
        
        var id = $(this).data('id');

        if($(this).prop('checked')){
         allCheckeds.includes(id) || allCheckeds.push(id);
        }else{
          allCheckeds.splice(allCheckeds.indexOf(id), 1);
        }

        console.log(allCheckeds.length,id);

        allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
        $btnDelete.prop('search','?id=' + allCheckeds);
      })

      //一键全选
      $('thead input').on('change',function(){
        var checked = $(this).prop('checked');
        $tbodyCheckboxs.prop('checked',checked).trigger('change');
      })

    })
  </script>
</body>
</html>
